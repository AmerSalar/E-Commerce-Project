import { useEffect, useState } from "react";
import api from "../api/axios";
import { useNavigate } from "react-router-dom";
import { useAuth } from "../context/AuthContext";
import Notification from "../components/Notification";

export default function Home() {
  const navigate = useNavigate();
  const { isAuthenticated } = useAuth();
  const [selectedButton, setSelectedButton] = useState(null);
  const [loading, setLoading] = useState(true);
  const [products, setProducts] = useState([]);
  const [alert, setAlert] = useState(null);
  const [quantity, setQuantity] = useState(1);

  const [page, setPage] = useState(1);
  useEffect(() => {
    try {
      const fetchProducts = async () => {
        const response = await api.get(`/products?page=${page}&perPage=16`);

        // fix this mess
        setProducts(response.data.data.data);

        console.log(response.data.data.data);
      };
      fetchProducts();
    } catch (error) {
      console.log("error: " + error);
    } finally {
      setLoading(false);
    }
  }, [page]);
  const addToCart = async (e, id) => {
    e.preventDefault();
    e.stopPropagation(); // no click bubbling

    setAlert(null);
    if (!isAuthenticated) {
      navigate("/login", { replace: false });
      return;
    }

    try {
      const response = await api.post("/carts/my-cart/" + id, {
        quantity: quantity,
      });

      setAlert({
        message: response.data.message,
        status: "success",
      });
    } catch (error) {
      switch (error.response?.status) {
        case 401:
          setAlert({
            message: "Unauthenticated!",
            status: "error",
          });
          break;
        case 422:
          setAlert({
            message: error.response.data.message,
            status: "error",
          });
          break;
        default:
          setAlert({
            message: "Something went wrong!",
            status: "error",
          });
      }
    } finally {
      setTimeout(() => {
        setAlert(null);
      }, 3000);
      setSelectedButton(null);
      setQuantity(1);
    }
  };
  return (
    <div className="bg-white">
      {alert && (
        <div className="fixed">
          <Notification label={alert.message} status={alert.status} />
        </div>
      )}

      <div className="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
        <h2 className="sr-only">Products</h2>

        <div className="grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 xl:gap-x-8">
          {products.map((product) => (
            <div key={product.id} className="group">
              <img
                alt={product.name + " cover"}
                src={
                  import.meta.env.VITE_BACKEND_URL + "/" + product.picture_url
                }
                className="aspect-square w-full rounded-lg bg-gray-200 object-cover xl:aspect-7/8"
              />
              <div className="flex flex-row justify-between">
                <div className="">
                  <h3 className="mt-4 text-sm text-gray-700">{product.name}</h3>
                  <p className="mt-1 text-lg font-medium text-gray-900">
                    {product.price}
                  </p>
                </div>
                {selectedButton !== product.id && (
                  <button
                    onClick={() => setSelectedButton(product.id)}
                    className="min-w-28 max-h-10 bg-indigo-500 hover:bg-indigo-400 text-white mt-4 rounded-2xl"
                  >
                    Add to cart
                  </button>
                )}

                {selectedButton === product.id && (
                  <div className="flex max-h-10 mt-4 rounded-2xl">
                    <input
                      className="w-10 text-center text-lg outline-0 border-2 border-r-0 rounded-tl-2xl rounded-bl-2xl"
                      type="number"
                      id="quantity"
                      name="quantity"
                      min={1}
                      max={10}
                      value={quantity}
                      onChange={(e) => setQuantity(Number(e.target.value))}
                    />
                    <button
                      onClick={(e) => addToCart(e, product.id)}
                      className="min-w-18 bg-indigo-500 hover:bg-indigo-400 rounded-2xl text-white"
                    >
                      Add
                    </button>
                  </div>
                )}
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}

import { useEffect, useState } from "react";
import api from "../api/axios";
import { useNavigate, useParams } from "react-router-dom";
import { useAuth } from "../context/AuthContext";
import Notification from "../components/Notification";

export default function Product() {
  const navigate = useNavigate();
  const [product, setProduct] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedButton, setSelectedButton] = useState(null);
  const [alert, setAlert] = useState(null);
  const [quantity, setQuantity] = useState(1);
  const { isAuthenticated } = useAuth();
  const { id } = useParams();

  useEffect(() => {
    const fetchProduct = async () => {
      try {
        const response = await api.get("/products/" + Number(id));

        setProduct(response.data);
      } catch (error) {
        console.log(error);
      } finally {
        setLoading(false);
      }
    };

    fetchProduct();
  }, [id]);

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
    <>
      {alert && (
        <div className="fixed">
          <Notification label={alert.message} status={alert.status} />
        </div>
      )}
      <div className="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
        <div className="flex">
          <img
            alt={product.name + " cover"}
            src={import.meta.env.VITE_BACKEND_URL + "/" + product.picture_url}
            className="aspect-square w-full rounded-lg bg-gray-200 object-cover xl:aspect-7/8 max-w-lg"
          />
          <div className="ml-4 flex flex-col">
            <h3 className="mt-4 text-lg text-gray-700">{product.name}</h3>
            <p className="mt-1 text-sm font-medium text-gray-900">
              {product.description}
            </p>
            <p className="mt-4 text-lg font-medium text-gray-900">
              ${product.price}
            </p>
            <p className="mt-1 text-md text-gray-900">
              {product.quantity} units
            </p>

            {selectedButton !== product.id && (
              <button
                onClick={() => setSelectedButton(product.id)}
                className="w-48 h-10 bg-indigo-500 hover:bg-indigo-400 text-white mt-4 rounded-2xl"
              >
                Add to cart
              </button>
            )}

            {selectedButton === product.id && (
              <div className="flex h-10 mt-4 rounded-2xl">
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
                  className="w-38 bg-indigo-500 hover:bg-indigo-400 rounded-2xl text-white"
                >
                  Add
                </button>
              </div>
            )}
          </div>
        </div>
      </div>
    </>
  );
}

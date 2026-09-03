import { useEffect, useState } from "react";
import api from "../api/axios";
import { useNavigate } from "react-router-dom";
import { useAuth } from "../context/AuthContext";

export default function Home() {
  const navigate = useNavigate();
  const { isAuthenticated } = useAuth();
  const [loading, setLoading] = useState(true);
  const [products, setProducts] = useState([]);
  const [alert, setAlert] = useState(null);
  const colors = {
    success: "#70bb90",
    fail: "#dd9070",
    info: "#baba60",
  };
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
  const addToCart = async (id) => {
    setAlert(null);
    if (!isAuthenticated) {
      navigate("/login", { replace: false });
    } else {
      try {
        const response = await api.post("/carts/my-cart/" + id);

        console.log(response.data.message);

        setTimeout(() => {
          setAlert(null);
        }, 1000);
        setAlert({
          message: "item added to cart successfully!",
          color: colors.success,
        });
      } catch (error) {
        if (error.response?.status === 401) {
          console.log("Unauthenticated!");
        } else {
          console.log("error: " + error);
        }
      }
    }
  };
  const getMyCart = async () => {
    try {
      const response = await api.get("/carts/my-cart");

      console.log(response.data.items);
    } catch (error) {
      if (error.response?.status === 401) {
        console.log("Unauthenticated!");
      } else {
        console.log("error: " + error);
      }
    }
  };
  return (
    <div className="bg-white">
      {alert && (
        <div
          style={{ backgroundColor: alert.color }}
          className="flex left-0  fixed w-[40%] h-10 items-center rounded-2xl justify-between px-5 "
        >
          <p className="text-white">{alert.message}</p>
          <button
            className="text-white px-4 py-2 rounded-2xl hover:text-zinc-200"
            onClick={() => setAlert(null)}
          >
            x
          </button>
        </div>
      )}

      <div className="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
        <h2 className="sr-only">Products</h2>

        <div className="grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 xl:gap-x-8">
          {products.map((product) => (
            <a key={product.id} href={product.href} className="group">
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
                <button
                  onClick={() => addToCart(product.id)}
                  className="min-w-28 max-h-10 bg-indigo-500 hover:bg-indigo-400 text-white mt-4 rounded-2xl"
                >
                  Add to cart
                </button>
              </div>
            </a>
          ))}
        </div>
      </div>
    </div>
  );
}

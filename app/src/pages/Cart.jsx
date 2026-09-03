import { useEffect, useState } from "react";
import api from "../api/axios";
import { Link } from "react-router-dom";
import Notification from "../components/Notification";

export default function Cart() {
  const [loading, setLoading] = useState(true);
  const [items, setItems] = useState([]);
  const [abandonOpen, setAbandonOpen] = useState(false);
  const [alert, setAlert] = useState(null);

  const fetchItems = async () => {
    setLoading(true);
    try {
      const response = await api.get(`/carts/my-cart`);
      setItems(response.data.items);
      console.log(response.data.items);
    } catch (error) {
      console.log("error: " + error);
    } finally {
      setLoading(false);
    }
  };
  useEffect(() => {
    fetchItems();
  }, []);
  const addToCart = async (id) => {
    setAlert(null);
    try {
      const response = await api.post("/carts/my-cart/" + id);

      console.log(response.data.message);

      fetchItems();
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
    }
  };
  const handleAbandon = async () => {
    try {
      await api.delete("/carts/my-cart");
      setAbandonOpen((prev) => !prev);
      fetchItems();

      setAlert({
        message: "Cart reset successfully!",
        status: "success",
      });
    } catch (error) {
      setAlert({
        message: "Something went wrong!",
        status: "error",
      });
    } finally {
      setTimeout(() => {
        setAlert(null);
      }, 3000);
    }
  };
  return (
    <>
      {alert && (
        <div className="fixed">
          <Notification label={alert.message} status={alert.status} />
        </div>
      )}
      {items.length > 0 && (
        <>
          <div className="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
            <h2 className="sr-only">Products</h2>

            <div className="grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 xl:gap-x-8">
              {items.map((product) => (
                <a key={product.id} href={product.href} className="group">
                  {product.quantity > 1 && (
                    <div className="flex absolute bg-[#00000090] w-12 h-12 justify-center items-center rounded-full">
                      <p className="text-2xl text-white font-black">
                        {product.quantity}x
                      </p>
                    </div>
                  )}

                  <img
                    alt={product.name + " cover"}
                    src={
                      import.meta.env.VITE_BACKEND_URL +
                      "/" +
                      product.picture_url
                    }
                    className="aspect-square w-full rounded-lg bg-gray-200 object-cover xl:aspect-7/8"
                  />
                  <div className="flex flex-row justify-between">
                    <div className="">
                      <h3 className="mt-4 text-sm text-gray-700">
                        {product.name}
                      </h3>
                      <p className="mt-1 text-lg font-medium text-gray-900">
                        ${product.price}
                      </p>
                    </div>
                    <button
                      onClick={() => addToCart(product.id)}
                      className="min-w-28 max-h-10 bg-indigo-500 hover:bg-indigo-400 text-white mt-4 rounded-2xl"
                    >
                      Add more
                    </button>
                  </div>
                </a>
              ))}
            </div>
            <div className="flex gap-2">
              {!abandonOpen && (
                <button
                  onClick={() => setAbandonOpen((prev) => !prev)}
                  className="bg-rose-600 hover:bg-rose-500 text-white p-2 px-20 mt-10 rounded-2xl"
                >
                  Abandon
                </button>
              )}
              {abandonOpen && (
                <div className="flex mt-10 gap-2">
                  <button
                    onClick={() => setAbandonOpen((prev) => !prev)}
                    className="p-2 px-12 bg-indigo-500 hover:bg-indigo-400 text-white rounded-2xl"
                  >
                    Cancel
                  </button>
                  <button
                    onClick={handleAbandon}
                    className="p-2 bg-rose-600 hover:bg-rose-500 rounded-2xl text-white"
                  >
                    Confirm
                  </button>
                </div>
              )}

              <Link
                to={"/my-cart/checkout"}
                className="bg-indigo-500 hover:bg-indigo-400 text-white p-2 px-30 mt-10 rounded-2xl"
              >
                Checkout
              </Link>
            </div>
          </div>
        </>
      )}
      {items.length === 0 && loading === false && (
        <div className="text-center mt-10">Cart is empty!</div>
      )}
    </>
  );
}

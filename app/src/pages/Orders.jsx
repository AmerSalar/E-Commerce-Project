import { useEffect, useState } from "react";
import api from "../api/axios";
import { Link, useParams } from "react-router-dom";
import Notification from "../components/Notification";

export default function Orders() {
  const [loading, setLoading] = useState(true);
  const [order, setOrder] = useState({ items: [] });
  const [abandonOpen, setAbandonOpen] = useState(false);
  const [alert, setAlert] = useState(null);
  const { id } = useParams();

  const fetchItems = async () => {
    setLoading(true);
    try {
      const response = await api.get(`/orders/` + id);
      setOrder(response.data);
      console.log(response.data);
    } catch (error) {
      console.log("error: " + error);
    } finally {
      setLoading(false);
    }
  };
  useEffect(() => {
    fetchItems();
  }, [id]);

  const handleCancel = async () => {
    try {
      const response = await api.delete("/orders/cancel/" + id);

      setAlert({
        message: response.data.message,
        status: "success",
      });

      await fetchItems();
    } catch (error) {
      setAlert({
        message: error.response.data.message,
        status: "error",
      });
    } finally {
      setTimeout(() => {
        setAlert(null);
      }, 3000);
      setAbandonOpen((prev) => !prev);
    }
  };
  return (
    <>
      {alert && (
        <div className="fixed">
          <Notification label={alert.message} status={alert.status} />
        </div>
      )}
      {order.items.length > 0 && (
        <>
          <div className="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
            <div className="bg-gray-100 p-4">
              <div className="grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 xl:gap-x-8">
                {order.items.map((product) => (
                  <div key={product.id} className="group">
                    {product.quantity > 1 && (
                      <div className="flex absolute bg-[#00000090] w-12 h-12 justify-center items-center rounded-full">
                        <p className="text-2xl text-white font-black">
                          {product.quantity}x
                        </p>
                      </div>
                    )}

                    <img
                      alt={product.item_name + " cover"}
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
                          {product.item_name}
                        </h3>
                        <p className="mt-1 text-lg font-medium text-gray-900">
                          ${product.item_price}
                        </p>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
              <div className="flex mt-10 gap-20">
                {order.status === "pending" && (
                  <div className="flex gap-2 w-48">
                    {!abandonOpen && (
                      <button
                        onClick={() => setAbandonOpen((prev) => !prev)}
                        className="bg-rose-600 hover:bg-rose-500 text-white p-2 px-20 rounded-2xl"
                      >
                        Cancel
                      </button>
                    )}
                    {abandonOpen && (
                      <div className="flex gap-2">
                        <button
                          onClick={() => setAbandonOpen((prev) => !prev)}
                          className="p-2 px-12 bg-indigo-500 hover:bg-indigo-400 text-white rounded-2xl"
                        >
                          Back
                        </button>
                        <button
                          onClick={handleCancel}
                          className="p-2 bg-rose-600 hover:bg-rose-500 rounded-2xl text-white"
                        >
                          Confirm
                        </button>
                      </div>
                    )}
                  </div>
                )}

                <p className="flex items-center">Order is {order.status}</p>
              </div>
            </div>
          </div>
        </>
      )}
      {order.items.length === 0 && loading === false && (
        <div className="text-center mt-10">Order does not exist!</div>
      )}
    </>
  );
}

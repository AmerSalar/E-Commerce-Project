import { useEffect, useState } from "react";
import api from "../api/axios";
import { Link, useParams } from "react-router-dom";
import Notification from "../components/Notification";
import OrderCard from "../components/OrderCard";

export default function Order() {
  const [loading, setLoading] = useState(true);
  const [order, setOrder] = useState({ items: [] });
  const [alert, setAlert] = useState(null);
  const [abandonOpen, setAbandonOpen] = useState(false);

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
            <OrderCard
              order={order}
              handleCancel={handleCancel}
              abandonOpen={abandonOpen}
              setAbandonOpen={setAbandonOpen}
            />
          </div>
        </>
      )}
      {order.items.length === 0 && loading === false && (
        <div className="text-center mt-10">Order does not exist!</div>
      )}
    </>
  );
}

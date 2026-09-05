import { useEffect, useState } from "react";
import api from "../api/axios";
import OrderCard from "../components/OrderCard";

export default function Orders() {
  const [loading, setLoading] = useState(true);
  const [orders, setOrders] = useState([]);

  const fetchOrders = async () => {
    setLoading(true);
    try {
      const response = await api.get(`/orders`);
      setOrders(response.data.orders);
      console.log(response.data.orders);
    } catch (error) {
      console.log("error: " + error);
    } finally {
      setLoading(false);
    }
  };
  useEffect(() => {
    fetchOrders();
  }, []);

  return (
    <>
      {orders.length > 0 && (
        <>
          <div className="flex flex-col gap-4 mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
            {orders.map((order) => {
              return <OrderCard order={order} />;
            })}
          </div>
        </>
      )}
      {orders.length === 0 && loading === false && (
        <div className="text-center mt-10">You don't have any orders!</div>
      )}
    </>
  );
}

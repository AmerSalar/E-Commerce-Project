import { useEffect, useState } from "react";
import api from "../api/axios";

export default function Cart() {
  const [loading, setLoading] = useState(true);
  const [items, setItems] = useState([]);
  const [checkoutOpen, setCheckoutOpen] = useState(false);
  const [abandonOpen, setAbandonOpen] = useState(false);
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
    try {
      const response = await api.post("/carts/my-cart/" + id);

      console.log(response.data.message);

      fetchItems();
    } catch (error) {
      if (error.response?.status === 401) {
        console.log("Unauthenticated!");
      } else {
        console.log("error: " + error);
      }
    }
  };
  const handleAbandon = async () => {
    try {
      await api.delete("/carts/my-cart");
      setAbandonOpen((prev) => !prev);
      fetchItems();
    } catch (error) {
      console.log("error: " + error);
    }
  };
  return (
    <div className="">
      {checkoutOpen && (
        <>
          {/*
        This example requires updating your template:

        ```
        <html class="h-full bg-white">
        <body class="h-full">
        ```
      */}
          <div className="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
            <div className="sm:mx-auto sm:w-full sm:max-w-sm">
              <h2 className="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900">
                Please provide an address
              </h2>
            </div>

            <div className="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
              <form onSubmit={handleSubmit} className="space-y-6">
                <div>
                  <label
                    htmlFor="phone"
                    className="block text-sm/6 font-medium text-gray-900"
                  >
                    Phone Number
                  </label>
                  <div className="mt-2">
                    <input
                      id="phone"
                      name="phone"
                      type="text"
                      required
                      autoComplete="phone"
                      onChange={"handleChange"}
                      className="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                    />
                  </div>
                </div>

                <div>
                  <div className="flex items-center justify-between">
                    <label
                      htmlFor="city"
                      className="block text-sm/6 font-medium text-gray-900"
                    >
                      City
                    </label>
                  </div>
                  <div className="mt-2">
                    <input
                      id="city"
                      name="city"
                      type="text"
                      required
                      autoComplete="city"
                      onChange={"handleChange"}
                      className="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                    />
                  </div>
                </div>

                <div>
                  <div className="flex items-center justify-between">
                    <label
                      htmlFor="street"
                      className="block text-sm/6 font-medium text-gray-900"
                    >
                      Street
                    </label>
                  </div>
                  <div className="mt-2">
                    <input
                      id="street"
                      name="street"
                      type="text"
                      required
                      autoComplete="street"
                      onChange={"handleChange"}
                      className="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                    />
                  </div>
                </div>
                <div>
                  <div className="flex items-center justify-between">
                    <label
                      htmlFor="building"
                      className="block text-sm/6 font-medium text-gray-900"
                    >
                      Building
                    </label>
                  </div>
                  <div className="mt-2">
                    <input
                      id="building"
                      name="building"
                      type="text"
                      required
                      autoComplete="building"
                      onChange={"handleChange"}
                      className="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                    />
                  </div>
                </div>
                <div>
                  <button
                    type="submit"
                    className="mt-10 flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                  >
                    Order Now
                  </button>
                </div>
              </form>
              <div className="flex gap-2 mt-4 text-sm">
                <p>Forgot something?</p>
                <button
                  className="text-indigo-600 hover:text-indigo-400"
                  onClick={() => setCheckoutOpen((prev) => !prev)}
                >
                  Go back
                </button>
              </div>
            </div>
          </div>
        </>
      )}
      {!checkoutOpen && (
        <>
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

                  <button
                    onClick={() => setCheckoutOpen((prev) => !prev)}
                    className="bg-indigo-500 hover:bg-indigo-400 text-white p-2 px-30 mt-10 rounded-2xl"
                  >
                    Checkout
                  </button>
                </div>
              </div>
            </>
          )}
          {items.length === 0 && loading === false && <div>Cart is empty!</div>}
        </>
      )}
    </div>
  );
}

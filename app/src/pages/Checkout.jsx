import { useEffect, useState } from "react";
import api from "../api/axios";
import { Link } from "react-router-dom";
import Selection from "../components/Selection";
import { useAuth } from "../context/AuthContext";

export default function Checkout() {
  const { user } = useAuth();
  const [loading, setLoading] = useState(true);
  const [address, setAddress] = useState({
    phone: "",
    city: "",
    street: "",
    building: 0,
  });
  const [selectedAddress, setSelectedAddress] = useState(null);

  useEffect(() => {
    if (user.addresses) {
      setSelectedAddress(user.addresses[0]);
    }
  }, []);

  const handleChange = (e) => {
    setAddress((prev) => ({
      ...prev,
      [e.target.name]: e.target.value,
    }));
  };
  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    try {
      const response = await api.post("/orders/order-now", address);

      console.log(response);
    } catch (error) {
      console.log("error: " + error);
    } finally {
      setLoading(false);
    }
  };
  return (
    <div className="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
      <div className="sm:mx-auto sm:w-full sm:max-w-sm">
        <h2 className="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900">
          Please provide an address
        </h2>
      </div>

      <div className="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <div className="flex justify-center">
          <Selection
            label="Select from your addresses"
            items={user.addresses}
            selected={selectedAddress}
            onChange={(item) => {
              (setSelectedAddress(item), setAddress(item));
            }}
            noOptionLabel="You don't have a saved address!"
          />
        </div>

        <form onSubmit={handleSubmit} className="space-y-6 mt-8">
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
                value={address.phone !== "" ? address.phone : ""}
                required
                autoComplete="phone"
                onChange={handleChange}
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
                value={address.city !== "" ? address.city : ""}
                required
                autoComplete="city"
                onChange={handleChange}
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
                value={address.street !== "" ? address.street : ""}
                required
                autoComplete="street"
                onChange={handleChange}
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
                type="number"
                value={address.building !== "" ? address.building : ""}
                required
                autoComplete="building"
                onChange={handleChange}
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
          <Link className="text-indigo-600 hover:text-indigo-400" to="/">
            Continue shopping
          </Link>
        </div>
      </div>
    </div>
  );
}

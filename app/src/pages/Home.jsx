import { useEffect, useState } from "react";
import api from "../api/axios";

export default function Home() {
  const [loading, setLoading] = useState(true);
  const [products, setProducts] = useState([]);
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
  return (
    <div className="bg-white">
      <div className="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
        <h2 className="sr-only">Products</h2>

        <div className="grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 xl:gap-x-8">
          {products.map((product) => (
            <a key={product.id} href={product.href} className="group">
              <img
                alt={"Videogame cover"}
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
                <button className="min-w-28 max-h-10 bg-indigo-500 text-white mt-4 rounded-2xl">
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

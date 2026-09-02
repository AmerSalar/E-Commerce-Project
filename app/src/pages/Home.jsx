import { useEffect, useState } from "react";
import api from "../api/axios";

export default function Home() {
  const [loading, setLoading] = useState(true);
  const [products, setProducts] = useState([]);
  useEffect(() => {
    try {
      const fetchProducts = async () => {
        const response = await api.get("/products?inlcude=categories&page=3");

        setProducts(response.data.data.data);

        console.log(response.data.data.data);
      };
      fetchProducts();
    } catch (error) {
      console.log("error: " + error);
    } finally {
      setLoading(false);
    }
  }, []);
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
                className="aspect-square w-full rounded-lg bg-gray-200 object-cover group-hover:opacity-75 xl:aspect-7/8"
              />
              <h3 className="mt-4 text-sm text-gray-700">{product.name}</h3>
              <p className="mt-1 text-lg font-medium text-gray-900">
                {product.price}
              </p>
            </a>
          ))}
        </div>
      </div>
    </div>
  );
}

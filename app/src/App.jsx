import { createBrowserRouter, RouterProvider } from "react-router-dom";
import RootLayout from "./layouts/RootLayout";
import Register from "./pages/Register";
import Login from "./pages/Login";
import Home from "./pages/Home";
import Profile from "./pages/Profile";
import Cart from "./pages/Cart";
import Checkout from "./pages/Checkout";
import Product from "./pages/Product";
import Order from "./pages/Order";
import Orders from "./pages/Orders";

// placeholder components
function NotFound() {
  return (
    <h1 className="text-xl font-bold text-red-600">404 - Page Not Found 🚫</h1>
  );
}

export const router = createBrowserRouter([
  {
    path: "/",
    element: <RootLayout />,
    errorElement: <NotFound />,
    children: [
      {
        index: true,
        element: <Home />,
      },
      {
        path: "me",
        element: <Profile />,
      },
      {
        path: "my-cart",
        element: <Cart />,
      },
      {
        path: "my-cart/checkout",
        element: <Checkout />,
      },
      {
        path: "products/:id",
        element: <Product />,
      },
      {
        path: "orders/:id",
        element: <Order />,
      },
      {
        path: "orders",
        element: <Orders />,
      },
    ],
  },
  {
    path: "/register",
    element: <Register />,
  },
  {
    path: "/login",
    element: <Login />,
  },
]);

export default function App() {
  return <RouterProvider router={router} />;
}

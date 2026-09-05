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
import EditProfile from "./pages/EditProfile";

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
        path: ":page",
        element: <Home />,
      },
      {
        path: "me",
        element: <Profile />,
      },
      {
        path: "me/edit",
        element: <EditProfile />,
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

import { createBrowserRouter, RouterProvider } from "react-router-dom";
import RootLayout from "./layouts/RootLayout";
import Register from "./pages/Register";
import Login from "./pages/Login";

// placeholder components
function Home() {
  return <h1 className="text-xl font-bold">Welcome Home 🏠</h1>;
}

function NotFound() {
  return (
    <h1 className="text-xl font-bold text-red-600">404 - Page Not Found 🚫</h1>
  );
}

const router = createBrowserRouter([
  {
    path: "/",
    element: <RootLayout />,
    errorElement: <NotFound />,
    children: [
      {
        index: true,
        element: <Home />,
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

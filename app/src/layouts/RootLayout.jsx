import { Outlet, Link } from "react-router-dom";
import { useAuth } from "../context/AuthContext";
export default function RootLayout() {
  const { isAuthenticated } = useAuth();
  return (
    <div className="min-h-screen bg-gray-50 text-gray-900">
      {/* Top Navigation */}
      <nav className="flex justify-between border-b border-gray-200 bg-white p-4">
        <div className="flex gap-4">
          <Link
            to="/"
            className="font-semibold text-indigo-500 hover:text-indigo-300"
          >
            Home
          </Link>
          <Link
            to="/my-cart"
            className="font-semibold text-indigo-500 hover:text-indigo-300"
          >
            Cart
          </Link>
          <Link
            to="/me"
            className="font-semibold text-indigo-500 hover:text-indigo-300"
          >
            Profile
          </Link>
        </div>
        {!isAuthenticated && (
          <div className="flex gap-6">
            <Link
              to="/login"
              className="font-semibold bg-indigo-500 hover:bg-indigo-400 px-6 text-white rounded-lg"
            >
              Login
            </Link>
            <Link
              to="/register"
              className="font-semibold text-indigo-500 hover:text-indigo-300"
            >
              Register
            </Link>
          </div>
        )}
      </nav>

      {/* Dynamic Child Page Content */}
      <main className="p-4">
        <Outlet />
      </main>
    </div>
  );
}

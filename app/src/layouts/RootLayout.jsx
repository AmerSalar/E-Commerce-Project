import { Outlet, Link } from "react-router-dom";

export default function RootLayout() {
  return (
    <div className="min-h-screen bg-gray-50 text-gray-900">
      {/* Top Navigation */}
      <nav className="flex gap-4 border-b border-gray-200 bg-white p-4">
        <Link
          to="/"
          className="font-semibold text-indigo-500 hover:text-indigo-300"
        >
          Home
        </Link>
        <Link
          to="/me"
          className="font-semibold text-indigo-500 hover:text-indigo-300"
        >
          Profile
        </Link>
      </nav>

      {/* Dynamic Child Page Content */}
      <main className="p-4">
        <Outlet />
      </main>
    </div>
  );
}

import { Navigate, useNavigate } from "react-router-dom";
import { useAuth } from "../context/AuthContext";
import { useState } from "react";

export default function Profile() {
  const navigate = useNavigate();
  const { user, logout } = useAuth();
  const [confirmOpen, setConfirmOpen] = useState(false);
  const handleLogout = async () => {
    await logout();
    navigate("/", { replace: true });
  };

  return (
    <div className="flex flex-col items-center text-center gap-1 mt-10">
      {user && (
        <>
          <h1 className="text-4xl font-bold text-zinc-800">{user.name}</h1>
          <h1 className="text-2xl text-zinc-800">{user.email}</h1>
          {user.roles.length > 0 && (
            <>
              <h1 className="text-2xl text-zinc-800 mt-4">Roles:</h1>
              {user.roles.map((role) => (
                <h1
                  key={role.id}
                  className="bg-gray-200 rounded-2xl p-2 text-lg text-zinc-800"
                >
                  {role.name}
                </h1>
              ))}
            </>
          )}
          {user.addresses.length > 0 && (
            <>
              <h1 className="text-2xl text-zinc-800 mt-4">Addresses:</h1>
              <div className="flex flex-wrap gap-4 ">
                {user.addresses.map((address) => (
                  <div key={address.id} className="bg-gray-200 p-4 rounded-2xl">
                    <h1 className="text-lg text-zinc-800">{address.phone}</h1>
                    <h1 className="text-lg text-zinc-800">{address.city}</h1>
                    <h1 className="text-lg text-zinc-800">{address.street}</h1>
                    <h1 className="text-lg text-zinc-800">
                      Building {address.building}
                    </h1>
                  </div>
                ))}
              </div>
            </>
          )}
          <button
            className="bg-indigo-500 hover:bg-indigo-400 mt-10  text-white p-2 px-20 rounded-2xl"
            onClick={() => navigate("/me/edit", { replace: false })}
          >
            Edit
          </button>
          <div className="flex flex-col justify-center w-48 gap-2 mt-1">
            <button
              className="bg-indigo-500 hover:bg-indigo-400 text-white p-2 rounded-2xl"
              onClick={() => setConfirmOpen((prev) => !prev)}
            >
              Log-out
            </button>
            {confirmOpen && (
              <button
                onClick={handleLogout}
                className="bg-rose-500 hover:bg-rose-400 text-white p-2 rounded-2xl"
              >
                Confirm
              </button>
            )}
          </div>
        </>
      )}
      {!user && <h1 className="text-4xl font-bold text-zinc-800">Guest</h1>}
    </div>
  );
}

import api from "../api/axios";
import { useAuth } from "../context/AuthContext";

export default function Profile() {
  const { user } = useAuth();
  return (
    <div className="flex flex-col items-center text-center gap-1 mt-10">
      {user && (
        <>
          <h1 className="text-4xl font-bold text-zinc-800">{user.name}</h1>
          <h1 className="text-2xl text-zinc-800">{user.email}</h1>
          {user.roles && (
            <>
              <h1 className="text-2xl text-zinc-800 mt-4">Roles:</h1>
              {user.roles.map((role) => (
                <h1 className="text-lg text-zinc-800">{role.name}</h1>
              ))}
            </>
          )}
          {user.addresses && (
            <>
              <h1 className="text-2xl text-zinc-800 mt-4">Addresses:</h1>
              <div className="flex flex-wrap gap-4 mt-4">
                {user.addresses.map((address) => (
                  <div className="border p-4 rounded-2xl">
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
        </>
      )}
      {!user && (
        <>
          <h1 className="text-4xl font-bold text-zinc-800">Guest Profile</h1>
        </>
      )}
    </div>
  );
}

import api from "../api/axios";
import { useAuth } from "../context/AuthContext";

export default function Profile() {
  const { user } = useAuth();
  return (
    <div className="text-2xl text-indigo-950">
      Welcome {user ? user.name : "our guest"}
    </div>
  );
}

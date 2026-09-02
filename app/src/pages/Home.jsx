import { Link, Navigate } from "react-router-dom";
import { useState } from "react";
import api from "../api/axios";

export default function Home() {
  return (
    <div>
      <button
        onClick={async () => {
          try {
            const response = await api.get("/carts/my-cart");

            console.log(response.data);
          } catch (error) {
            if (error.response?.status === 401) {
              console.log("Unauthenticated!");
            } else {
              console.log("status: " + error);
            }
          }
        }}
      >
        Get cart
      </button>
    </div>
  );
}

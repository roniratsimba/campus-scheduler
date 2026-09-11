import { Outlet } from "react-router-dom";
import Sidebar from "./Sidebar";

export default function Layout() {
  return (
    <div className="flex min-h-screen bg-gray-50">
      <Sidebar />
      <main className="flex-1 lg:ml-64 p-4 lg:p-8 pt-16 lg:pt-8">
        <Outlet />
      </main>
    </div>
  );
}
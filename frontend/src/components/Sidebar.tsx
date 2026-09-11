import { Link, useLocation } from "react-router-dom";
import { useState } from "react";
import { 
  LayoutDashboard, 
  Users, 
  BookOpen, 
  Building2, 
  Users2, 
  Calendar, 
  FileText, 
  Search,
  LogOut,
  Home,
  Menu,
  X
} from "lucide-react";
import { ConfirmationModal } from "./ui/ConfirmationModal";

const menuItems = [
  { 
    text: "Dashboard", 
    icon: LayoutDashboard,
    path: "/dashboard" 
  },
  { 
    text: "Enseignants", 
    icon: Users,
    path: "/teachers" 
  },
  { 
    text: "Matières", 
    icon: BookOpen,
    path: "/subjects" 
  },
  { 
    text: "Salles", 
    icon: Building2,
    path: "/rooms" 
  },
  { 
    text: "Groupes", 
    icon: Users2,
    path: "/academic-groups" 
  },
  { 
    text: "Semaines", 
    icon: Calendar,
    path: "/schedule-weeks" 
  },
  { 
    text: "Emploi du temps", 
    icon: FileText,
    path: "/course-sessions" 
  },
  { 
    text: "Salles libres", 
    icon: Search,
    path: "/free-rooms" 
  },
];

export default function Sidebar() {
  const location = useLocation();
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
  const [showLogoutModal, setShowLogoutModal] = useState(false);

  const handleLogout = () => {
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    setIsMobileMenuOpen(false);
    window.location.href = "/";
  };

  return (
    <>
      {/* Mobile Menu Button */}
      <button
        className="lg:hidden fixed top-4 left-4 z-50 p-2 rounded-lg bg-white shadow-md border border-gray-200"
        onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
      >
        {isMobileMenuOpen ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
      </button>

      {/* Mobile Overlay */}
      {isMobileMenuOpen && (
        <div
          className="lg:hidden fixed inset-0 bg-black/50 z-40"
          onClick={() => setIsMobileMenuOpen(false)}
        />
      )}

      {/* Sidebar */}
      <aside
        className={`
          glass fixed left-0 top-0 h-full
          w-64 flex flex-col p-6
          transition-transform duration-300 ease-in-out
          ${isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'}
          z-50
        `}
      >
        {/* Logo */}
        <div className="mb-8">
          <h2 className="text-night-950 text-lg font-medium tracking-wide">
            Campus Scheduler
          </h2>
          <p className="text-emerald-600 text-xs font-medium tracking-widest uppercase mt-1">
            Administration
          </p>
        </div>

        {/* Navigation */}
        <nav className="flex-1">
          <ul className="space-y-1">
            {menuItems.map((item) => {
              const isActive = location.pathname === item.path;
              const Icon = item.icon;
              return (
                <li key={item.text}>
                  <Link
                    to={item.path}
                    onClick={() => setIsMobileMenuOpen(false)}
                    className={`
                      flex items-center px-3 py-2.5 rounded-lg
                      transition-all duration-200
                      ${isActive 
                        ? 'bg-emerald-50 text-emerald-700 font-medium' 
                        : 'text-gray-600 hover:bg-gray-50 hover:text-night-950'
                      }
                    `}
                  >
                    <Icon 
                      className={`w-5 h-5 mr-3 ${isActive ? 'text-emerald-600' : 'text-gray-400'}`} 
                    />
                    <span>{item.text}</span>
                  </Link>
                </li>
              );
            })}
          </ul>
        </nav>

        {/* Footer Actions */}
        <div className="space-y-2 pt-4 border-t border-gray-200">
          <button
            onClick={() => setShowLogoutModal(true)}
            className="w-full flex items-center px-3 py-2.5 rounded-lg
              text-gray-600 hover:bg-red-50 hover:text-red-600
              transition-all duration-200 border border-gray-200 hover:border-red-200"
          >
            <LogOut className="w-5 h-5 mr-3" />
            <span>Se déconnecter</span>
          </button>

          <Link
            to="/"
            onClick={() => setIsMobileMenuOpen(false)}
            className="w-full flex items-center px-3 py-2.5 rounded-lg
              text-gray-600 hover:bg-gray-50 hover:text-night-950
              transition-all duration-200 border border-gray-200"
          >
            <Home className="w-5 h-5 mr-3" />
            <span>Retour accueil</span>
          </Link>
        </div>
      </aside>

      {/* Logout Confirmation Modal */}
      <ConfirmationModal
        isOpen={showLogoutModal}
        onClose={() => setShowLogoutModal(false)}
        onConfirm={handleLogout}
        title="Confirmation de déconnexion"
        message="Êtes-vous sûr de vouloir vous déconnecter ?"
        confirmText="Se déconnecter"
        cancelText="Annuler"
        variant="warning"
      />
    </>
  );
}
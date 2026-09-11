import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { api } from "../service/api";
import { Card } from "../components/ui/Card";
import { Button } from "../components/ui/Button";
import { GraduationCap, User, Building2, ArrowRight } from "lucide-react";

type Group = {
  id: number;
  displayName: string;
  level: string;
  program: string;
};

type Teacher = {
  id: number;
  firstName: string;
  lastName: string;
  email: string;
};

type Room = {
  id: number;
  name: string;
  code: string;
  type: string;
};

export default function HomePage() {
  const navigate = useNavigate();
  const [groups, setGroups] = useState<Group[]>([]);
  const [teachers, setTeachers] = useState<Teacher[]>([]);
  const [rooms, setRooms] = useState<Room[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    Promise.all([
      api.get("/public/groups"),
      api.get("/public/teachers"),
      api.get("/public/rooms"),
    ])
      .then(([groupsRes, teachersRes, roomsRes]) => {
        setGroups(groupsRes.data);
        setTeachers(teachersRes.data);
        setRooms(roomsRes.data);
      })
      .finally(() => {
        setLoading(false);
      });
  }, []);

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <div className="w-12 h-12 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
          <p className="text-gray-600">Chargement...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
      <div className="max-w-6xl mx-auto px-4 py-16 mesh-gradient">
        {/* Hero Section */}
        <div className="text-center mb-16 relative z-10">
          <h1 className="text-5xl font-light text-night-950 mb-4 tracking-tight">
            Campus Scheduler
          </h1>
          <p className="text-lg text-gray-600 max-w-2xl mx-auto">
            Consultez les emplois du temps par groupe, enseignant ou salle
          </p>
        </div>

        {/* Selection Cards */}
        <div className="grid md:grid-cols-3 gap-6 mb-16 relative z-10">
          <Card variant="gradient" className="hover:shadow-lg transition-shadow duration-300">
            <div className="flex items-center mb-6">
              <div className="p-3 rounded-lg bg-night-100 text-night-950 mr-4">
                <GraduationCap className="w-6 h-6" />
              </div>
              <h3 className="text-lg font-medium text-night-950">Par Groupe</h3>
            </div>
            <select
              onChange={(e) => {
                if (e.target.value) {
                  navigate(`/public/group/${e.target.value}`);
                }
              }}
              className="w-full px-4 py-3 border border-gray-200 rounded-lg bg-white text-night-950 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
            >
              <option value="">Sélectionner un groupe...</option>
              {groups.map((group) => (
                <option key={group.id} value={group.id}>
                  {group.displayName} ({group.level} - {group.program})
                </option>
              ))}
            </select>
          </Card>

          <Card variant="gradient" className="hover:shadow-lg transition-shadow duration-300">
            <div className="flex items-center mb-6">
              <div className="p-3 rounded-lg bg-emerald-100 text-emerald-700 mr-4">
                <User className="w-6 h-6" />
              </div>
              <h3 className="text-lg font-medium text-night-950">Par Enseignant</h3>
            </div>
            <select
              onChange={(e) => {
                if (e.target.value) {
                  navigate(`/public/teacher/${e.target.value}`);
                }
              }}
              className="w-full px-4 py-3 border border-gray-200 rounded-lg bg-white text-night-950 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
            >
              <option value="">Sélectionner un enseignant...</option>
              {teachers.map((teacher) => (
                <option key={teacher.id} value={teacher.id}>
                  {teacher.firstName} {teacher.lastName}
                </option>
              ))}
            </select>
          </Card>

          <Card variant="gradient" className="hover:shadow-lg transition-shadow duration-300">
            <div className="flex items-center mb-6">
              <div className="p-3 rounded-lg bg-amber-100 text-amber-700 mr-4">
                <Building2 className="w-6 h-6" />
              </div>
              <h3 className="text-lg font-medium text-night-950">Par Salle</h3>
            </div>
            <select
              onChange={(e) => {
                if (e.target.value) {
                  navigate(`/public/room/${e.target.value}`);
                }
              }}
              className="w-full px-4 py-3 border border-gray-200 rounded-lg bg-white text-night-950 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
            >
              <option value="">Sélectionner une salle...</option>
              {rooms.map((room) => (
                <option key={room.id} value={room.id}>
                  {room.name} ({room.code})
                </option>
              ))}
            </select>
          </Card>
        </div>

        {/* CTA Section */}
        <div className="text-center relative z-10">
          <Button 
            variant="accent" 
            onClick={() => navigate("/login")}
            className="group"
          >
            Accès Administration
            <ArrowRight className="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" />
          </Button>
        </div>
      </div>
    </div>
  );
}

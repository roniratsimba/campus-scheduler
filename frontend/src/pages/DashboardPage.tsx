import { useEffect, useState } from "react";
import { api } from "../service/api";
import { Card } from "../components/ui/Card";
import { Users, BookOpen, Building2, Users2, Calendar, FileText } from "lucide-react";

export default function DashboardPage() {
  const [stats, setStats] = useState({
    teachers: 0,
    subjects: 0,
    rooms: 0,
    groups: 0,
    weeks: 0,
    sessions: 0
  });
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    Promise.all([
      api.get("/teachers"),
      api.get("/subjects"),
      api.get("/rooms"),
      api.get("/academic-groups"),
      api.get("/schedule-weeks"),
      api.get("/course-sessions")
    ])
      .then(([teachersRes, subjectsRes, roomsRes, groupsRes, weeksRes, sessionsRes]) => {
        setStats({
          teachers: teachersRes.data.length,
          subjects: subjectsRes.data.length,
          rooms: roomsRes.data.length,
          groups: groupsRes.data.length,
          weeks: weeksRes.data.length,
          sessions: sessionsRes.data.length
        });
      })
      .finally(() => {
        setLoading(false);
      });
  }, []);

  if (loading) {
    return (
      <div className="flex items-center justify-center min-h-[200px]">
        <div className="text-center">
          <div className="w-12 h-12 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
          <p className="text-gray-600">Chargement...</p>
        </div>
      </div>
    );
  }

  const statCards = [
    { label: "Enseignants", value: stats.teachers, color: "text-night-500", bgColor: "bg-night-50", icon: Users },
    { label: "Matières", value: stats.subjects, color: "text-emerald-600", bgColor: "bg-emerald-50", icon: BookOpen },
    { label: "Salles", value: stats.rooms, color: "text-amber-600", bgColor: "bg-amber-50", icon: Building2 },
    { label: "Groupes", value: stats.groups, color: "text-night-500", bgColor: "bg-night-50", icon: Users2 },
    { label: "Semaines", value: stats.weeks, color: "text-emerald-600", bgColor: "bg-emerald-50", icon: Calendar },
    { label: "Séances", value: stats.sessions, color: "text-amber-600", bgColor: "bg-amber-50", icon: FileText },
  ];

  return (
    <div className="mesh-gradient relative z-10">
      <h1 className="text-3xl font-light text-night-950 mb-8">Tableau de bord</h1>
      
      <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        {statCards.map((stat, index) => {
          const Icon = stat.icon;
          return (
            <Card 
              key={index} 
              variant="gradient"
              className={`text-center hover:shadow-lg transition-shadow duration-300 border-l-4 ${stat.color.replace('text-', 'border-')}`}
            >
              <div className={`inline-flex p-3 rounded-lg ${stat.bgColor} ${stat.color} mb-4`}>
                <Icon className="w-6 h-6" />
              </div>
              <div className={`text-4xl font-light ${stat.color} mb-2`}>
                {stat.value}
              </div>
              <div className="text-sm font-medium text-gray-600">
                {stat.label}
              </div>
            </Card>
          );
        })}
      </div>
    </div>
  );
}
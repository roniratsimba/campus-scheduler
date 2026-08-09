import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { api } from "../service/api";

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
    return <div className="loading">Chargement...</div>;
  }

  return (
    <div className="container">
      <div style={{ textAlign: "center", marginBottom: "3rem" }}>
        <h1>Campus Scheduler</h1>
        <p>Consultez les emplois du temps par groupe, enseignant ou salle</p>
      </div>

      <div className="grid grid-3">
        <div className="card">
          <h3>🎓 Par Groupe</h3>
          <select
            onChange={(e) => {
              if (e.target.value) {
                navigate(`/public/group/${e.target.value}`);
              }
            }}
            style={{ width: "100%", marginTop: "1rem" }}
          >
            <option value="">Sélectionner un groupe...</option>
            {groups.map((group) => (
              <option key={group.id} value={group.id}>
                {group.displayName} ({group.level} - {group.program})
              </option>
            ))}
          </select>
        </div>

        <div className="card">
          <h3>👨‍🏫 Par Enseignant</h3>
          <select
            onChange={(e) => {
              if (e.target.value) {
                navigate(`/public/teacher/${e.target.value}`);
              }
            }}
            style={{ width: "100%", marginTop: "1rem" }}
          >
            <option value="">Sélectionner un enseignant...</option>
            {teachers.map((teacher) => (
              <option key={teacher.id} value={teacher.id}>
                {teacher.firstName} {teacher.lastName}
              </option>
            ))}
          </select>
        </div>

        <div className="card">
          <h3>🏢 Par Salle</h3>
          <select
            onChange={(e) => {
              if (e.target.value) {
                navigate(`/public/room/${e.target.value}`);
              }
            }}
            style={{ width: "100%", marginTop: "1rem" }}
          >
            <option value="">Sélectionner une salle...</option>
            {rooms.map((room) => (
              <option key={room.id} value={room.id}>
                {room.name} ({room.code})
              </option>
            ))}
          </select>
        </div>
      </div>

      <div className="flex-center" style={{ marginTop: "3rem" }}>
        <button className="btn-primary" onClick={() => navigate("/login")}>
          🔐 Accès Administration
        </button>
      </div>
    </div>
  );
}

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
    return <div>Chargement...</div>;
  }

  return (
    <div style={{ padding: "2rem" }}>
      <h1>Consultation des emplois du temps</h1>

      <div style={{ marginBottom: "2rem" }}>
        <h2>Par groupe</h2>
        <select
          onChange={(e) => navigate(`/public/group/${e.target.value}`)}
          style={{ padding: "0.5rem", minWidth: "300px" }}
        >
          <option value="">Sélectionner un groupe...</option>
          {groups.map((group) => (
            <option key={group.id} value={group.id}>
              {group.displayName} ({group.level} - {group.program})
            </option>
          ))}
        </select>
      </div>

      <div style={{ marginBottom: "2rem" }}>
        <h2>Par enseignant</h2>
        <select
          onChange={(e) => navigate(`/public/teacher/${e.target.value}`)}
          style={{ padding: "0.5rem", minWidth: "300px" }}
        >
          <option value="">Sélectionner un enseignant...</option>
          {teachers.map((teacher) => (
            <option key={teacher.id} value={teacher.id}>
              {teacher.firstName} {teacher.lastName} ({teacher.email})
            </option>
          ))}
        </select>
      </div>

      <div style={{ marginBottom: "2rem" }}>
        <h2>Par salle</h2>
        <select
          onChange={(e) => navigate(`/public/room/${e.target.value}`)}
          style={{ padding: "0.5rem", minWidth: "300px" }}
        >
          <option value="">Sélectionner une salle...</option>
          {rooms.map((room) => (
            <option key={room.id} value={room.id}>
              {room.name} ({room.code} - {room.type})
            </option>
          ))}
        </select>
      </div>

      <div style={{ marginTop: "2rem" }}>
        <button
          onClick={() => navigate("/login")}
          style={{ padding: "0.5rem 1rem" }}
        >
          Accès administration
        </button>
      </div>
    </div>
  );
}

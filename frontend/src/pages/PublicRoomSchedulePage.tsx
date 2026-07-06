import { useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router-dom";
import { api } from "../service/api";

type CourseSession = {
  id: number;
  teacher: string;
  subject: string;
  room: string | null;
  deliveryMode: string;
  dayOfWeek: string;
  startTime: string;
  endTime: string;
  groups: string[];
};

type Room = {
  id: number;
  name: string;
  code: string;
  type: string;
};

const DAYS = [
  "MONDAY",
  "TUESDAY",
  "WEDNESDAY",
  "THURSDAY",
  "FRIDAY",
  "SATURDAY",
];

const DAY_LABELS: Record<string, string> = {
  MONDAY: "Lundi",
  TUESDAY: "Mardi",
  WEDNESDAY: "Mercredi",
  THURSDAY: "Jeudi",
  FRIDAY: "Vendredi",
  SATURDAY: "Samedi",
};

const SLOTS = ["08:00", "10:00", "14:00", "16:00"];

export default function PublicRoomSchedulePage() {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const [room, setRoom] = useState<Room | null>(null);
  const [sessions, setSessions] = useState<CourseSession[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (!id) return;

    api.get(`/public/schedule/room/${id}`)
      .then((res) => {
        setRoom(res.data.room);
        setSessions(res.data.sessions);
      })
      .catch(() => {
        navigate("/");
      })
      .finally(() => {
        setLoading(false);
      });
  }, [id, navigate]);

  const findSessions = (day: string, slot: string) => {
    return sessions.filter(
      (s) =>
        s.dayOfWeek === day &&
        s.startTime.startsWith(slot)
    );
  };

  if (loading) {
    return <div>Chargement...</div>;
  }

  if (!room) {
    return <div>Salle non trouvée</div>;
  }

  return (
    <div>
      <h1>Emploi du temps - {room.name}</h1>
      <p>
        {room.code} - {room.type}
      </p>

      <table border={1} cellPadding={10}>
        <thead>
          <tr>
            <th>Heure</th>
            {DAYS.map((day) => (
              <th key={day}>{DAY_LABELS[day]}</th>
            ))}
          </tr>
        </thead>

        <tbody>
          {SLOTS.map((slot) => (
            <tr key={slot}>
              <td>{slot}</td>

              {DAYS.map((day) => {
                const cellSessions = findSessions(day, slot);

                return (
                  <td
                    key={`${day}-${slot}`}
                    style={{
                      verticalAlign: "top",
                      minWidth: "180px",
                    }}
                  >
                    {cellSessions.map((session) => (
                      <div
                        key={session.id}
                        style={{
                          border: "1px solid #ddd",
                          padding: "0.5rem",
                          marginBottom: "0.5rem",
                          borderRadius: "4px",
                          background: "#fff",
                        }}
                      >
                        <strong>{session.subject}</strong>
                        <br />
                        {session.teacher}
                        <br />
                        {session.groups.join(", ")}
                      </div>
                    ))}
                  </td>
                );
              })}
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

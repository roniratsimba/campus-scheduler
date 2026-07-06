import { useEffect, useState } from "react";
import { api } from "../service/api";

type Room = {
  id: number;
  name: string;
  code: string;
  type: string;
};

type ScheduleWeek = {
  id: number;
  startDate: string;
  endDate: string;
  status: string;
};

const DAYS = [
  { value: "MONDAY", label: "Lundi" },
  { value: "TUESDAY", label: "Mardi" },
  { value: "WEDNESDAY", label: "Mercredi" },
  { value: "THURSDAY", label: "Jeudi" },
  { value: "FRIDAY", label: "Vendredi" },
  { value: "SATURDAY", label: "Samedi" },
];

const TIME_SLOTS = [
  { start: "08:00", end: "10:00", label: "08:00 - 10:00" },
  { start: "10:00", end: "12:00", label: "10:00 - 12:00" },
  { start: "14:00", end: "16:00", label: "14:00 - 16:00" },
  { start: "16:00", end: "18:00", label: "16:00 - 18:00" },
];

export default function FreeRoomsPage() {
  const [weeks, setWeeks] = useState<ScheduleWeek[]>([]);
  const [freeRooms, setFreeRooms] = useState<Room[]>([]);
  const [selectedDay, setSelectedDay] = useState("");
  const [selectedSlot, setSelectedSlot] = useState("");
  const [selectedWeek, setSelectedWeek] = useState<number | null>(null);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    api.get("/schedule-weeks").then((res) => {
      setWeeks(res.data);
    });
  }, []);

  const handleSearch = () => {
    if (!selectedDay || !selectedSlot || !selectedWeek) return;

    setLoading(true);
    const slot = TIME_SLOTS.find((s) => s.label === selectedSlot);
    
    api
      .get("/public/rooms/free", {
        params: {
          dayOfWeek: selectedDay,
          startTime: slot?.start,
          endTime: slot?.end,
          weekId: selectedWeek,
        },
      })
      .then((res) => {
        setFreeRooms(res.data);
      })
      .finally(() => {
        setLoading(false);
      });
  };

  return (
    <div style={{ padding: "2rem" }}>
      <h1>Recherche de salles libres</h1>

      <div style={{ marginBottom: "2rem" }}>
        <div style={{ marginBottom: "1rem" }}>
          <label style={{ display: "block", marginBottom: "0.5rem" }}>
            Semaine
          </label>
          <select
            value={selectedWeek || ""}
            onChange={(e) => setSelectedWeek(Number(e.target.value))}
            style={{ padding: "0.5rem", minWidth: "300px" }}
          >
            <option value="">Sélectionner une semaine...</option>
            {weeks.map((week) => (
              <option key={week.id} value={week.id}>
                {week.startDate} - {week.endDate} ({week.status})
              </option>
            ))}
          </select>
        </div>

        <div style={{ marginBottom: "1rem" }}>
          <label style={{ display: "block", marginBottom: "0.5rem" }}>
            Jour
          </label>
          <select
            value={selectedDay}
            onChange={(e) => setSelectedDay(e.target.value)}
            style={{ padding: "0.5rem", minWidth: "300px" }}
          >
            <option value="">Sélectionner un jour...</option>
            {DAYS.map((day) => (
              <option key={day.value} value={day.value}>
                {day.label}
              </option>
            ))}
          </select>
        </div>

        <div style={{ marginBottom: "1rem" }}>
          <label style={{ display: "block", marginBottom: "0.5rem" }}>
            Créneau horaire
          </label>
          <select
            value={selectedSlot}
            onChange={(e) => setSelectedSlot(e.target.value)}
            style={{ padding: "0.5rem", minWidth: "300px" }}
          >
            <option value="">Sélectionner un créneau...</option>
            {TIME_SLOTS.map((slot) => (
              <option key={slot.label} value={slot.label}>
                {slot.label}
              </option>
            ))}
          </select>
        </div>

        <button
          onClick={handleSearch}
          disabled={!selectedDay || !selectedSlot || !selectedWeek || loading}
          style={{
            padding: "0.5rem 1rem",
            background: "#007bff",
            color: "white",
            border: "none",
            cursor: loading ? "not-allowed" : "pointer",
          }}
        >
          {loading ? "Recherche..." : "Rechercher"}
        </button>
      </div>

      {freeRooms.length > 0 && (
        <div>
          <h2>Salles libres ({freeRooms.length})</h2>
          <table border={1} cellPadding={10}>
            <thead>
              <tr>
                <th>Nom</th>
                <th>Code</th>
                <th>Type</th>
              </tr>
            </thead>
            <tbody>
              {freeRooms.map((room) => (
                <tr key={room.id}>
                  <td>{room.name}</td>
                  <td>{room.code}</td>
                  <td>{room.type}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      {freeRooms.length === 0 && selectedDay && selectedSlot && selectedWeek && !loading && (
        <p>Aucune salle libre pour ce créneau.</p>
      )}
    </div>
  );
}

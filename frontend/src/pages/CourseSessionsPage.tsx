import { useEffect, useState } from "react";
import { api } from "../service/api";
import CourseSessionModal from "./CourseSessionModal";

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
  weekId: number;
};

type ScheduleWeek = {
  id: number;
  startDate: string;
  endDate: string;
  status: string;
};

type SelectedCell = {
  day: string;
  slot: string;
} | null;

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

export default function TimetablePage() {
  const [showForm, setShowForm] = useState(false);
  const [sessions, setSessions] = useState<CourseSession[]>([]);
  const [weeks, setWeeks] = useState<ScheduleWeek[]>([]);
  const [selectedWeekId, setSelectedWeekId] = useState<number | undefined>(
    undefined
  );
  const [selectedCell, setSelectedCell] = useState<SelectedCell>(null);

  const loadSessions = () => {
    api.get("/course-sessions").then((res) => {
      setSessions(res.data);
    });
  };

  const loadWeeks = () => {
    api.get("/schedule-weeks").then((res) => {
      const weekList: ScheduleWeek[] = res.data;
      setWeeks(weekList);
      if (weekList.length > 0) {
        setSelectedWeekId((current) => current ?? weekList[0].id);
      }
    });
  };

  useEffect(() => {
    loadSessions();
    loadWeeks();
  }, []);

  const findSessions = (day: string, slot: string) => {
    return sessions.filter(
      (s) =>
        s.weekId === selectedWeekId &&
        s.dayOfWeek === day &&
        s.startTime.startsWith(slot)
    );
  };

  const openCell = (day: string, slot: string) => {
    setSelectedCell({ day, slot });
    setShowForm(true);
  };

  return (
    <div>
      <h1>Emploi du temps</h1>

      <label>
        Semaine :{" "}
        <select
          value={selectedWeekId ?? ""}
          onChange={(e) => setSelectedWeekId(Number(e.target.value))}
        >
          {weeks.map((w) => (
            <option key={w.id} value={w.id}>
              {w.startDate} → {w.endDate} ({w.status})
            </option>
          ))}
        </select>
      </label>

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
                      cursor: "pointer",
                      verticalAlign: "top",
                      background: "#fafafa",
                    }}
                  >
                                            {/* Bouton ajouter pour nouvelle séance */}
                        <button
                          onClick={() => openCell(day, slot)}
                          style={{
                            width: "100%",
                            marginBottom: "0.5rem",
                          }}
                        >
                          +
                        </button>
                    {cellSessions.map((session) => (
                      
                      <div
                        key={session.id}
                        onClick={(e) => {
                          e.stopPropagation();
                          // édition plus tard
                        }}
                        style={{
                          border: "1px solid #ddd",
                          cursor: "pointer",
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
                        {session.room}
                      </div>
                    ))}
                  </td>
                );
              })}
            </tr>
          ))}
        </tbody>
      </table>

      <CourseSessionModal
        open={showForm}
        onClose={() => setShowForm(false)}
        onCreated={() => {
          loadSessions();
          setSelectedCell(null);
        }}
        selectedCell={selectedCell}
        selectedWeekId={selectedWeekId}
      />
    </div>
  );
}
import { useEffect, useState } from "react";
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

const SLOTS = [
  "08:00",
  "10:00",
  "14:00",
  "16:00",
];

export default function TimetablePage() {
  const [sessions, setSessions] = useState<CourseSession[]>([]);

  useEffect(() => {
    api.get("/course-sessions").then((response) => {
      setSessions(response.data);
    });
  }, []);

  const findSession = (day: string, slot: string) => {
    return sessions.find(
      (session) =>
        session.dayOfWeek === day &&
        session.startTime.startsWith(slot)
    );
  };

  return (
    <div>
      <h1>Emploi du temps</h1>

      <table border={1} cellPadding={10}>
        <thead>
          <tr>
            <th>Heure</th>

            {DAYS.map((day) => (
                <th key={day}>
                    {DAY_LABELS[day]}
                </th>
            ))}
          </tr>
        </thead>

        <tbody>
          {SLOTS.map((slot) => (
            <tr key={slot}>
              <td>{slot}</td>

              {DAYS.map((day) => {
                const session = findSession(day, slot);

                return (
                  <td key={`${day}-${slot}`}>
                    {session && (
                      <>
                        <strong>{session.subject}</strong>
                        <br />
                        {session.teacher}
                        <br />
                        {session.room}
                      </>
                    )}
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
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
  weekId: number;
  status: string;
};

export default function CourseSessionsPage() {
  const [sessions, setSessions] = useState<CourseSession[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    api
      .get("/course-sessions")
      .then((response) => {
        setSessions(response.data);
      })
      .finally(() => {
        setLoading(false);
      });
  }, []);

  if (loading) {
    return <p>Loading...</p>;
  }

  return (
    <div>
      <h1>Course Sessions</h1>

      <table>
        <thead>
          <tr>
            <th>Teacher</th>
            <th>Subject</th>
            <th>Room</th>
            <th>Groups</th>
            <th>Day</th>
            <th>Time</th>
            <th>Mode</th>
          </tr>
        </thead>

        <tbody>
          {sessions.map((session) => (
            <tr key={session.id}>
              <td>{session.teacher}</td>
              <td>{session.subject}</td>
              <td>{session.room ?? "-"}</td>
              <td>{session.groups.join(", ")}</td>
              <td>{session.dayOfWeek}</td>
              <td>
                {session.startTime} - {session.endTime}
              </td>
              <td>{session.deliveryMode}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
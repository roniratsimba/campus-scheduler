import { Link } from "react-router-dom";

export default function Sidebar() {
  return (
    <aside style={{ width: "250px", padding: "1rem" }}>
      <h2>Campus Scheduler</h2>

      <nav>
        <ul>
          <li><Link to="/">Dashboard</Link></li>
          <li><Link to="/teachers">Teachers</Link></li>
          <li><Link to="/subjects">Subjects</Link></li>
          <li><Link to="/rooms">Rooms</Link></li>
          <li><Link to="/academic-groups">Groups</Link></li>
          <li><Link to="/schedule-weeks">Weeks</Link></li>
          <li><Link to="/course-sessions">Timetable</Link></li>
        </ul>
      </nav>
    </aside>
  );
}
import { createBrowserRouter } from "react-router-dom";

import Layout from "../components/Layout";
import DashboardPage from "../pages/DashboardPage";
import TeachersPage from "../pages/TeachersPage";
import SubjectsPage from "../pages/SubjectsPage";
import RoomsPage from "../pages/RoomsPage";
import AcademicGroupsPage from "../pages/AcademicGroupsPage";
import ScheduleWeeksPage from "../pages/ScheduleWeeksPage";
import CourseSessionsPage from "../pages/CourseSessionsPage";

export const router = createBrowserRouter([
  {
    path: "/",
    element: <Layout />,
    children: [
      {
        index: true,
        element: <DashboardPage />,
      },
      {
        path: "teachers",
        element: <TeachersPage />,
      },
      {
        path: "subjects",
        element: <SubjectsPage />,
      },
      {
        path: "rooms",
        element: <RoomsPage />,
      },
      {
        path: "academic-groups",
        element: <AcademicGroupsPage />,
      },
      {
        path: "schedule-weeks",
        element: <ScheduleWeeksPage />,
      },
      {
        path: "course-sessions",
        element: <CourseSessionsPage />,
      },
    ],
  },
]);
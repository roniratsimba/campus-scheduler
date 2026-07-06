import { createBrowserRouter } from "react-router-dom";

import Layout from "../components/Layout";
import DashboardPage from "../pages/DashboardPage";
import TeachersPage from "../pages/TeachersPage";
import SubjectsPage from "../pages/SubjectsPage";
import RoomsPage from "../pages/RoomsPage";
import AcademicGroupsPage from "../pages/AcademicGroupsPage";
import ScheduleWeeksPage from "../pages/ScheduleWeeksPage";
import CourseSessionsPage from "../pages/CourseSessionsPage";
import HomePage from "../pages/HomePage";
import PublicGroupSchedulePage from "../pages/PublicGroupSchedulePage";
import PublicTeacherSchedulePage from "../pages/PublicTeacherSchedulePage";
import PublicRoomSchedulePage from "../pages/PublicRoomSchedulePage";
import LoginPage from "../pages/LoginPage";
import FreeRoomsPage from "../pages/FreeRoomsPage";

export const router = createBrowserRouter([
  {
    path: "/",
    element: <HomePage />,
  },
  {
    path: "/login",
    element: <LoginPage />,
  },
  {
    path: "/",
    element: <Layout />,
    children: [
      {
        path: "dashboard",
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
      {
        path: "free-rooms",
        element: <FreeRoomsPage />,
      },
    ],
  },
  {
    path: "/public/group/:id",
    element: <PublicGroupSchedulePage />,
  },
  {
    path: "/public/teacher/:id",
    element: <PublicTeacherSchedulePage />,
  },
  {
    path: "/public/room/:id",
    element: <PublicRoomSchedulePage />,
  },
]);
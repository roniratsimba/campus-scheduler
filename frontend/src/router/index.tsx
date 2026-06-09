import { createBrowserRouter } from 'react-router-dom';
import TeachersPage from '../pages/TeachersPage';

export const router = createBrowserRouter([
    {
        path: '/',
        element: <TeachersPage />,
    },
]);
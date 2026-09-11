import { useEffect, useState } from 'react';
import { Toast } from './Toast';
import { toast as toastSystem } from './ToastContainer';

export const ToastProvider = () => {
  const [toasts, setToasts] = useState(toastSystem.getToasts());

  useEffect(() => {
    const unsubscribe = toastSystem.subscribe(() => {
      setToasts(toastSystem.getToasts());
    });
    return unsubscribe;
  }, []);

  return (
    <div className="fixed top-4 right-4 z-50 flex flex-col gap-2">
      {toasts.map((toast) => (
        <Toast
          key={toast.id}
          type={toast.type}
          message={toast.message}
          duration={toast.duration}
          onClose={() => toastSystem.remove(toast.id)}
        />
      ))}
    </div>
  );
};
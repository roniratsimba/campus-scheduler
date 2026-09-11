import { useEffect, useState } from 'react';
import { X, CheckCircle, AlertCircle, Info } from 'lucide-react';

type ToastType = 'success' | 'error' | 'info';

interface ToastProps {
  type?: ToastType;
  message: string;
  duration?: number;
  onClose: () => void;
}

const toastIcons: Record<ToastType, any> = {
  success: CheckCircle,
  error: AlertCircle,
  info: Info,
};

const toastStyles: Record<ToastType, string> = {
  success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
  error: 'bg-red-50 border-red-200 text-red-800',
  info: 'bg-blue-50 border-blue-200 text-blue-800',
};

export const Toast = ({ type = 'info', message, duration = 3000, onClose }: ToastProps) => {
  const [isVisible, setIsVisible] = useState(true);

  useEffect(() => {
    const timer = setTimeout(() => {
      setIsVisible(false);
      setTimeout(onClose, 300); // Wait for animation
    }, duration);

    return () => clearTimeout(timer);
  }, [duration, onClose]);

  const Icon = toastIcons[type];

  return (
    <div
      className={`
        fixed top-4 right-4 z-50
        flex items-center gap-3 px-4 py-3
        border rounded-lg shadow-lg
        animate-slide-up
        ${toastStyles[type]}
        ${isVisible ? 'opacity-100' : 'opacity-0'}
        transition-opacity duration-300
      `}
      style={{ pointerEvents: 'auto' }}
    >
      <Icon className="w-5 h-5 flex-shrink-0" />
      <span className="text-sm font-medium">{message}</span>
      <button
        onClick={() => {
          setIsVisible(false);
          setTimeout(onClose, 300);
        }}
        className="ml-2 opacity-60 hover:opacity-100 transition-opacity"
      >
        <X className="w-4 h-4" />
      </button>
    </div>
  );
};
import { Toast } from './Toast';

type ToastType = 'success' | 'error' | 'info';

interface ToastMessage {
  id: number;
  type: ToastType;
  message: string;
  duration?: number;
}

let toasts: ToastMessage[] = [];
let toastIdCounter = 0;
let listeners: (() => void)[] = [];

const notifyListeners = () => {
  listeners.forEach(listener => listener());
};

export const toast = {
  success: (message: string, duration?: number) => {
    const id = ++toastIdCounter;
    toasts.push({ id, type: 'success', message, duration });
    notifyListeners();
  },
  error: (message: string, duration?: number) => {
    const id = ++toastIdCounter;
    toasts.push({ id, type: 'error', message, duration });
    notifyListeners();
  },
  info: (message: string, duration?: number) => {
    const id = ++toastIdCounter;
    toasts.push({ id, type: 'info', message, duration });
    notifyListeners();
  },
  remove: (id: number) => {
    toasts = toasts.filter((t) => t.id !== id);
    notifyListeners();
  },
  subscribe: (listener: () => void) => {
    listeners.push(listener);
    return () => {
      listeners = listeners.filter(l => l !== listener);
    };
  },
  getToasts: () => toasts,
};
import Modal from "../Modal";
import { Button } from "./Button";
import { AlertTriangle } from "lucide-react";

interface ConfirmationModalProps {
  isOpen: boolean;
  onClose: () => void;
  onConfirm: () => void;
  title: string;
  message: string;
  confirmText?: string;
  cancelText?: string;
  variant?: 'danger' | 'warning' | 'info';
}

const variantStyles = {
  danger: 'text-red-600 bg-red-50',
  warning: 'text-amber-600 bg-amber-50',
  info: 'text-blue-600 bg-blue-50',
};

export const ConfirmationModal = ({
  isOpen,
  onClose,
  onConfirm,
  title,
  message,
  confirmText = 'Confirmer',
  cancelText = 'Annuler',
  variant = 'danger',
}: ConfirmationModalProps) => {
  return (
    <Modal isOpen={isOpen} onClose={onClose} title={title} size="sm">
      <div className="space-y-4">
        <div className={`flex items-start gap-3 p-4 rounded-lg ${variantStyles[variant]}`}>
          <AlertTriangle className="w-5 h-5 flex-shrink-0 mt-0.5" />
          <p className="text-sm">{message}</p>
        </div>
        
        <div className="flex justify-end gap-3 pt-2">
          <Button variant="secondary" onClick={onClose}>
            {cancelText}
          </Button>
          <Button 
            variant={variant === 'danger' ? 'danger' : 'primary'} 
            onClick={() => {
              onConfirm();
              onClose();
            }}
          >
            {confirmText}
          </Button>
        </div>
      </div>
    </Modal>
  );
};
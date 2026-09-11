import { InputHTMLAttributes, forwardRef, ReactNode } from 'react';

interface InputProps extends InputHTMLAttributes<HTMLInputElement> {
  label?: string;
  error?: string;
  fullWidth?: boolean;
  icon?: ReactNode;
}

export const Input = forwardRef<HTMLInputElement, InputProps>(
  ({ label, error, fullWidth = false, icon, className = '', ...props }, ref) => {
    return (
      <div className={`${fullWidth ? 'w-full' : ''}`}>
        {label && (
          <label className="block text-sm font-medium text-night-950 mb-1.5">
            {label}
          </label>
        )}
        <div className="relative">
          {icon && (
            <div className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
              {icon}
            </div>
          )}
          <input
            ref={ref}
            className={`
              w-full px-4 py-2.5
              border border-gray-200 rounded-lg
              bg-white text-night-950
              placeholder:text-gray-400
              transition-all duration-200
              focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent
              disabled:bg-gray-50 disabled:cursor-not-allowed
              ${icon ? 'pl-10' : ''}
              ${error ? 'border-red-500 focus:ring-red-500' : ''}
              ${className}
            `}
            {...props}
          />
        </div>
        {error && (
          <p className="mt-1 text-xs text-red-500">{error}</p>
        )}
      </div>
    );
  }
);

Input.displayName = 'Input';
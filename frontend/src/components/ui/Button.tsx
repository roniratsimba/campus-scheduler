import { ButtonHTMLAttributes, forwardRef } from 'react';
import { Loader2 } from 'lucide-react';

type ButtonVariant = 'primary' | 'secondary' | 'accent' | 'danger' | 'ghost';
type ButtonSize = 'sm' | 'md' | 'lg';

interface ButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: ButtonVariant;
  size?: ButtonSize;
  loading?: boolean;
  fullWidth?: boolean;
}

const buttonStyles: Record<ButtonVariant, string> = {
  primary: 'bg-night-950 text-white border-night-950 hover:bg-night-900 hover:border-night-900 hover:shadow-lg',
  secondary: 'bg-white text-night-950 border-gray-200 hover:bg-gray-50 hover:border-gray-300',
  accent: 'bg-gradient-to-r from-night-500 to-night-600 text-white border-none hover:from-night-600 hover:to-night-700 hover:shadow-glow',
  danger: 'bg-red-500 text-white border-red-500 hover:bg-red-600 hover:border-red-600',
  ghost: 'bg-transparent text-night-950 border-transparent hover:bg-gray-100',
};

const sizeStyles: Record<ButtonSize, string> = {
  sm: 'px-3 py-1.5 text-xs',
  md: 'px-5 py-2.5 text-sm',
  lg: 'px-6 py-3 text-base',
};

export const Button = forwardRef<HTMLButtonElement, ButtonProps>(
  ({ 
    variant = 'primary', 
    size = 'md', 
    loading = false, 
    fullWidth = false,
    disabled,
    className = '',
    children,
    ...props 
  }, ref) => {
    return (
      <button
        ref={ref}
        disabled={disabled || loading}
        className={`
          inline-flex items-center justify-center
          font-medium rounded-lg
          border transition-all duration-200
          focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2
          disabled:opacity-50 disabled:cursor-not-allowed
          ${buttonStyles[variant]}
          ${sizeStyles[size]}
          ${fullWidth ? 'w-full' : ''}
          ${loading ? 'cursor-wait' : ''}
          ${className}
        `}
        {...props}
      >
        {loading && <Loader2 className="w-4 h-4 mr-2 animate-spin" />}
        {children}
      </button>
    );
  }
);

Button.displayName = 'Button';
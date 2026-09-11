import { HTMLAttributes } from 'react';

type BadgeVariant = 'default' | 'success' | 'warning' | 'danger' | 'accent';

interface BadgeProps extends HTMLAttributes<HTMLSpanElement> {
  variant?: BadgeVariant;
}

const badgeStyles: Record<BadgeVariant, string> = {
  default: 'bg-gray-100 text-gray-600',
  success: 'bg-emerald-100 text-emerald-700',
  warning: 'bg-amber-100 text-amber-700',
  danger: 'bg-red-100 text-red-700',
  accent: 'bg-indigo-100 text-indigo-700',
};

export const Badge = ({ variant = 'default', className = '', children, ...props }: BadgeProps) => {
  return (
    <span
      className={`
        inline-flex items-center px-2.5 py-0.5
        text-xs font-medium rounded-full
        ${badgeStyles[variant]}
        ${className}
      `}
      {...props}
    >
      {children}
    </span>
  );
};
import { HTMLAttributes } from 'react';

interface CardProps extends HTMLAttributes<HTMLDivElement> {
  variant?: 'default' | 'gradient' | 'glass';
}

const cardStyles: Record<string, string> = {
  default: 'bg-white border border-gray-200',
  gradient: 'card-gradient border border-emerald-200/30',
  glass: 'glass',
};

export const Card = ({ variant = 'default', className = '', children, ...props }: CardProps) => {
  return (
    <div
      className={`
        rounded-xl p-6 shadow-sm
        ${cardStyles[variant]}
        ${className}
      `}
      {...props}
    >
      {children}
    </div>
  );
};
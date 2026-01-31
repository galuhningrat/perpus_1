export default function Button({ type = 'button', className = '', children, variant = 'primary', ...props }) {
    const baseClasses = 'px-4 py-2 rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed';

    const variants = {
        primary: 'bg-blue-600 text-white hover:bg-blue-700',
        secondary: 'bg-gray-200 text-gray-700 hover:bg-gray-300',
        danger: 'bg-red-600 text-white hover:bg-red-700',
        success: 'bg-green-600 text-white hover:bg-green-700',
        outline: 'border border-blue-600 text-blue-600 hover:bg-blue-50',
    };

    return (
        <button
            type={type}
            className={`${baseClasses} ${variants[variant]} ${className}`}
            {...props}
        >
            {children}
        </button>
    );
}

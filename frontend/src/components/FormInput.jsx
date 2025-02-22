export default function FormInput({
  id,
  type = 'text',
  label,
  register,
  validation,
  error,
  ...props
}) {
  return (
    <div className="flex flex-col gap-1">
      <label htmlFor={id} className="block text-sm font-medium text-gray-700">
        {label}
      </label>
      <input
        id={id}
        type={type}
        {...register(id, validation)}
        className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        {...props}
      />{' '}
      {error && <p className="mt-1 text-xs text-red-500">{error.message}</p>}
    </div>
  )
}

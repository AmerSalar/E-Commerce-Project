import { useState, useRef, useEffect } from "react";

export default function ObjectSelect({
  label = "Select an item",
  items = [],
  selected,
  onChange,
  noOptionLabel = "No option!",
}) {
  const [isOpen, setIsOpen] = useState(false);
  const dropdownRef = useRef(null);

  // Close when clicking outside
  useEffect(() => {
    const handleClickOutside = (e) => {
      if (dropdownRef.current && !dropdownRef.current.contains(e.target)) {
        setIsOpen(false);
      }
    };
    document.addEventListener("mousedown", handleClickOutside);
    return () => document.removeEventListener("mousedown", handleClickOutside);
  }, []);

  const handleSelect = (item) => {
    onChange(item);
    setIsOpen(false);
  };

  return (
    <div className="w-full max-w-xs relative" ref={dropdownRef}>
      {label && (
        <label className="block text-sm font-medium text-gray-900 mb-1.5">
          {label}
        </label>
      )}

      {/* Button */}
      <button
        type="button"
        onClick={() => setIsOpen((prev) => !prev)}
        className="flex w-full items-center justify-between rounded-md border border-gray-300 bg-white px-3 py-2 text-left text-sm text-gray-900  transition hover:border-gray-400 focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600"
      >
        <span className="truncate">
          {selected
            ? `${selected.phone} - ${selected.city}`
            : "Select an option..."}
        </span>

        {/* Up / Down Arrow SVG */}
        <svg
          className={`h-4 w-4 text-gray-500 transition-transform duration-200 ${
            isOpen ? "rotate-180" : ""
          }`}
          fill="none"
          stroke="currentColor"
          strokeWidth="2"
          viewBox="0 0 24 24"
        >
          <path
            strokeLinecap="round"
            strokeLinejoin="round"
            d="M19 9l-7 7-7-7"
          />
        </svg>
      </button>

      {/* Dropdown Menu */}
      {isOpen && (
        <ul className="absolute z-20 mt-1 max-h-60 w-full overflow-auto rounded-md border border-gray-200 bg-white py-1 focus:outline-none text-sm">
          {items.length === 0 ? (
            <li className="px-3 py-2 text-gray-400 text-center">
              {noOptionLabel}
            </li>
          ) : (
            items.map((item) => {
              const isSelected = selected?.id === item.id;
              return (
                <li
                  key={item.id}
                  onClick={() => handleSelect(item)}
                  className={`flex cursor-pointer items-center justify-between px-3 py-2 select-none transition ${
                    isSelected
                      ? "bg-indigo-50 font-semibold text-indigo-700"
                      : "text-gray-900 hover:bg-indigo-600 hover:text-white"
                  }`}
                >
                  <span className="truncate">
                    {item.phone} - {item.city}
                  </span>

                  {/* Checkmark SVG */}
                  {isSelected && (
                    <svg
                      className="h-4 w-4 text-indigo-600"
                      fill="none"
                      stroke="currentColor"
                      strokeWidth="2.5"
                      viewBox="0 0 24 24"
                    >
                      <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        d="M5 13l4 4L19 7"
                      />
                    </svg>
                  )}
                </li>
              );
            })
          )}
        </ul>
      )}
    </div>
  );
}

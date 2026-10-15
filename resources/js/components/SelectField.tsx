import { useId } from 'react';
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from './ui/select';
import { cn } from '@/lib/utils';

// Radix reserves an empty item value for clearing the Select.
const ALL_ITEMS = '__all__';

export function SelectField({
  label,
  options,
  value,
  onValueChange,
  allLabel,
  disabled,
  name,
  className,
}: {
  label: string;
  options: { value: string; label: string }[];
  value: string;
  onValueChange: (value: string) => void;
  allLabel?: string;
  disabled?: boolean;
  name?: string;
  className?: string;
}) {
  const id = useId();
  const selectedLabel = options.find((option) => option.value === value)?.label ?? allLabel;
  return (
    <div className={cn('select-field', className)}>
      <label htmlFor={id}>{label}</label>
      <Select
        value={allLabel && !value ? ALL_ITEMS : value}
        onValueChange={(nextValue) =>
          onValueChange(allLabel && nextValue === ALL_ITEMS ? '' : nextValue)
        }
        disabled={disabled}
        name={name}
      >
        <SelectTrigger id={id} title={selectedLabel}>
          <SelectValue placeholder={allLabel}>{selectedLabel}</SelectValue>
        </SelectTrigger>
        <SelectContent>
          <SelectGroup>
            {allLabel && <SelectItem value={ALL_ITEMS}>{allLabel}</SelectItem>}
            {options.map((option) => (
              <SelectItem key={option.value} value={option.value}>
                {option.label}
              </SelectItem>
            ))}
          </SelectGroup>
        </SelectContent>
      </Select>
    </div>
  );
}

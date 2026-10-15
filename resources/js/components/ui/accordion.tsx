import * as React from 'react';
import * as AccordionPrimitive from '@radix-ui/react-accordion';
import { Plus } from '../icons';
import { cn } from '@/lib/utils';
export function Accordion(props: React.ComponentProps<typeof AccordionPrimitive.Root>) {
  return <AccordionPrimitive.Root data-slot="accordion" {...props} />;
}
export function AccordionItem({
  className,
  ...props
}: React.ComponentProps<typeof AccordionPrimitive.Item>) {
  return (
    <AccordionPrimitive.Item
      data-slot="accordion-item"
      className={cn('border-b border-border', className)}
      {...props}
    />
  );
}
export function AccordionTrigger({
  className,
  children,
  ...props
}: React.ComponentProps<typeof AccordionPrimitive.Trigger>) {
  return (
    <AccordionPrimitive.Header className="flex">
      <AccordionPrimitive.Trigger
        data-slot="accordion-trigger"
        className={cn(
          'group flex min-h-14 flex-1 items-center justify-between gap-5 py-6 text-left text-base font-medium text-foreground hover:underline focus-visible:outline-3 focus-visible:outline-offset-4 focus-visible:outline-ring',
          className,
        )}
        {...props}
      >
        {children}
        <Plus
          className="size-5 shrink-0 transition-transform duration-200 group-data-[state=open]:rotate-45"
          aria-hidden="true"
        />
      </AccordionPrimitive.Trigger>
    </AccordionPrimitive.Header>
  );
}
export function AccordionContent({
  className,
  children,
  ...props
}: React.ComponentProps<typeof AccordionPrimitive.Content>) {
  return (
    <AccordionPrimitive.Content
      data-slot="accordion-content"
      className="overflow-hidden text-base"
      {...props}
    >
      <div className={cn('pb-6 pr-6 leading-relaxed text-muted-foreground', className)}>
        {children}
      </div>
    </AccordionPrimitive.Content>
  );
}

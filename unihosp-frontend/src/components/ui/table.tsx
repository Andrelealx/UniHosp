import { cn } from "@/lib/utils";

type TableProps = React.TableHTMLAttributes<HTMLTableElement>;
type RowProps = React.HTMLAttributes<HTMLTableRowElement>;
type CellProps = React.TdHTMLAttributes<HTMLTableCellElement>;
type HeadCellProps = React.ThHTMLAttributes<HTMLTableCellElement>;

export function TableContainer({ className, ...props }: React.HTMLAttributes<HTMLDivElement>) {
  return <div className={cn("overflow-x-auto rounded-2xl border border-border", className)} {...props} />;
}

export function Table({ className, ...props }: TableProps) {
  return <table className={cn("w-full min-w-[680px] border-collapse", className)} {...props} />;
}

export function THead({ className, ...props }: React.HTMLAttributes<HTMLTableSectionElement>) {
  return <thead className={cn("bg-muted/70 text-left", className)} {...props} />;
}

export function TBody({ className, ...props }: React.HTMLAttributes<HTMLTableSectionElement>) {
  return <tbody className={cn("divide-y divide-border", className)} {...props} />;
}

export function Tr({ className, ...props }: RowProps) {
  return <tr className={cn("hover:bg-muted/40", className)} {...props} />;
}

export function Th({ className, ...props }: HeadCellProps) {
  return <th className={cn("px-4 py-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground", className)} {...props} />;
}

export function Td({ className, ...props }: CellProps) {
  return <td className={cn("px-4 py-3 text-sm", className)} {...props} />;
}

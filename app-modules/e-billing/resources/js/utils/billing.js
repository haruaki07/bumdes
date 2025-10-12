export function getBillingCycles({ due, grace }) {
  if (!due) return [];

  const today = new Date();
  let firstDue = new Date(today.getFullYear(), today.getMonth() + 1, due);

  const data = [];
  for (let i = 0; i < 6; i++) {
    const dueDate = new Date(
      firstDue.getFullYear(),
      firstDue.getMonth() + i,
      due
    );
    const isolationDate = new Date(
      dueDate.getFullYear(),
      dueDate.getMonth(),
      due + grace
    );
    const periodStart = new Date(
      dueDate.getFullYear(),
      dueDate.getMonth() - 1,
      due + 1
    );

    data.push({
      month: dueDate.getMonth(),
      duration: 1, // month
      due_date: dueDate,
      isolation_date: isolationDate,
      period: {
        start: periodStart,
        end: dueDate,
      },
    });
  }

  return data;
}

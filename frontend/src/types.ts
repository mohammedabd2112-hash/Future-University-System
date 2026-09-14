export interface Invoice {
  id: number;
  student_id: number;
  student_name?: string;
  student_email?: string;
  title: string;
  amount: number;
  academic_year: string;
  semester: string;
  status: 'unpaid' | 'partial' | 'paid';
  total_paid?: number;
  remaining_balance?: number;
  created_at: string;
}

export interface Payment {
  id: number;
  invoice_id: number;
  student_id: number;
  amount_paid: number;
  payment_method: 'نقداً' | 'تحويل بنكي' | 'شيك';
  receipt_number: string;
  payment_date: string;
  notes?: string;
  created_at: string;
}

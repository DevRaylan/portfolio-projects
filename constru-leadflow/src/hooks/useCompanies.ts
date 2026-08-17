import { useQuery } from "@tanstack/react-query";
import { supabase } from "@/integrations/supabase/client";
import { COMPANIES_QUERY_KEY } from "@/lib/queryKeys";

export interface Company {
  id: string;
  name: string;
  cnpj: string | null;
  address: string | null;
  city: string | null;
  state: string | null;
  phone: string | null;
  email: string | null;
}

export const useCompanies = () =>
  useQuery({
    queryKey: COMPANIES_QUERY_KEY,
    queryFn: async () => {
      const { data, error } = await supabase
        .from("companies")
        .select("*")
        .order("name", { ascending: true });

      if (error) throw error;
      return (data ?? []) as Company[];
    },
  });

import { useQuery } from "@tanstack/react-query";
import { supabase } from "@/integrations/supabase/client";
import { CONSTRUCTIONS_QUERY_KEY } from "@/lib/queryKeys";

export interface Construction {
  id: string;
  name: string;
  location: string;
  city: string;
  state: string;
  latitude: number;
  longitude: number;
  status: "high" | "medium" | "low";
  estimated_value: number | null;
  estimated_demand: number | null;
  contact_name: string | null;
  contact_phone: string | null;
  contact_email: string | null;
  buyer_name: string | null;
  buyer_phone: string | null;
  buyer_email: string | null;
  notes: string | null;
  company_id: string | null;
  created_at: string;
}

export const useConstructions = () =>
  useQuery({
    queryKey: CONSTRUCTIONS_QUERY_KEY,
    queryFn: async () => {
      const { data, error } = await supabase
        .from("constructions")
        .select("*")
        .order("created_at", { ascending: false })
        .limit(500);

      if (error) throw error;
      return (data ?? []) as Construction[];
    },
  });

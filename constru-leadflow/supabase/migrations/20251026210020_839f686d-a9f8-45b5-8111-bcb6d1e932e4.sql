-- Criar tabela de empresas
CREATE TABLE public.companies (
  id UUID NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
  user_id UUID REFERENCES auth.users(id) ON DELETE CASCADE,
  name TEXT NOT NULL,
  cnpj TEXT,
  address TEXT,
  city TEXT,
  state TEXT,
  phone TEXT,
  email TEXT,
  created_at TIMESTAMP WITH TIME ZONE DEFAULT now(),
  updated_at TIMESTAMP WITH TIME ZONE DEFAULT now()
);

-- Habilitar RLS na tabela de empresas
ALTER TABLE public.companies ENABLE ROW LEVEL SECURITY;

-- Políticas RLS para empresas
CREATE POLICY "Users can view their own companies"
  ON public.companies FOR SELECT
  USING (auth.uid() = user_id);

CREATE POLICY "Users can create companies"
  ON public.companies FOR INSERT
  WITH CHECK (auth.uid() = user_id);

CREATE POLICY "Users can update their own companies"
  ON public.companies FOR UPDATE
  USING (auth.uid() = user_id);

CREATE POLICY "Users can delete their own companies"
  ON public.companies FOR DELETE
  USING (auth.uid() = user_id);

-- Adicionar campos de comprador na tabela constructions
ALTER TABLE public.constructions 
  ADD COLUMN buyer_name TEXT,
  ADD COLUMN buyer_phone TEXT,
  ADD COLUMN buyer_email TEXT,
  ADD COLUMN company_id UUID REFERENCES public.companies(id) ON DELETE SET NULL;

-- Trigger para atualizar updated_at na tabela companies
CREATE TRIGGER update_companies_updated_at
  BEFORE UPDATE ON public.companies
  FOR EACH ROW
  EXECUTE FUNCTION public.update_updated_at_column();
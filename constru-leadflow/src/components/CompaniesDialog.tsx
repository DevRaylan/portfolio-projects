import { useState, useEffect } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import * as z from "zod";
import { supabase } from "@/integrations/supabase/client";
import { useToast } from "@/hooks/use-toast";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { Building2, Pencil, Trash2 } from "lucide-react";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from "@/components/ui/alert-dialog";

const companySchema = z.object({
  name: z.string().min(2, "Nome deve ter no mínimo 2 caracteres"),
  cnpj: z.string().optional(),
  address: z.string().optional(),
  city: z.string().optional(),
  state: z.string().optional(),
  phone: z.string().optional(),
  email: z.string().email("Email inválido").optional().or(z.literal("")),
});

type CompanyFormData = z.infer<typeof companySchema>;

interface Company {
  id: string;
  name: string;
  cnpj: string | null;
  address: string | null;
  city: string | null;
  state: string | null;
  phone: string | null;
  email: string | null;
}

const CompaniesDialog = () => {
  const [open, setOpen] = useState(false);
  const [companies, setCompanies] = useState<Company[]>([]);
  const [editingCompany, setEditingCompany] = useState<Company | null>(null);
  const [deleteId, setDeleteId] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);
  const { toast } = useToast();

  const form = useForm<CompanyFormData>({
    resolver: zodResolver(companySchema),
    defaultValues: {
      name: "",
      cnpj: "",
      address: "",
      city: "",
      state: "",
      phone: "",
      email: "",
    },
  });

  useEffect(() => {
    if (open) {
      fetchCompanies();
    }
  }, [open]);

  useEffect(() => {
    if (editingCompany) {
      form.reset({
        name: editingCompany.name,
        cnpj: editingCompany.cnpj || "",
        address: editingCompany.address || "",
        city: editingCompany.city || "",
        state: editingCompany.state || "",
        phone: editingCompany.phone || "",
        email: editingCompany.email || "",
      });
    } else {
      form.reset({
        name: "",
        cnpj: "",
        address: "",
        city: "",
        state: "",
        phone: "",
        email: "",
      });
    }
  }, [editingCompany, form]);

  const fetchCompanies = async () => {
    try {
      const { data: { user } } = await supabase.auth.getUser();
      if (!user) return;

      const { data, error } = await supabase
        .from("companies")
        .select("*")
        .order("name", { ascending: true });

      if (error) throw error;
      setCompanies((data || []) as Company[]);
    } catch (error: any) {
      toast({
        title: "Erro ao carregar empresas",
        description: error.message,
        variant: "destructive",
      });
    }
  };

  const onSubmit = async (data: CompanyFormData) => {
    setLoading(true);
    try {
      const { data: { user } } = await supabase.auth.getUser();
      if (!user) throw new Error("Usuário não autenticado");

      if (editingCompany) {
        const { error } = await supabase
          .from("companies")
          .update(data)
          .eq("id", editingCompany.id);

        if (error) throw error;
        toast({
          title: "Empresa atualizada!",
          description: "A empresa foi atualizada com sucesso.",
        });
      } else {
        const { error } = await supabase
          .from("companies")
          .insert([{ 
            name: data.name,
            cnpj: data.cnpj || null,
            address: data.address || null,
            city: data.city || null,
            state: data.state || null,
            phone: data.phone || null,
            email: data.email || null,
            user_id: user.id 
          }]);

        if (error) throw error;
        toast({
          title: "Empresa cadastrada!",
          description: "A empresa foi adicionada com sucesso.",
        });
      }

      form.reset();
      setEditingCompany(null);
      fetchCompanies();
    } catch (error: any) {
      toast({
        title: "Erro ao salvar empresa",
        description: error.message,
        variant: "destructive",
      });
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async () => {
    if (!deleteId) return;

    try {
      const { error } = await supabase
        .from("companies")
        .delete()
        .eq("id", deleteId);

      if (error) throw error;

      toast({
        title: "Empresa excluída!",
        description: "A empresa foi removida com sucesso.",
      });

      fetchCompanies();
    } catch (error: any) {
      toast({
        title: "Erro ao excluir empresa",
        description: error.message,
        variant: "destructive",
      });
    } finally {
      setDeleteId(null);
    }
  };

  return (
    <>
      <Dialog open={open} onOpenChange={setOpen}>
        <DialogTrigger asChild>
          <Button variant="outline" className="gap-2">
            <Building2 className="h-4 w-4" />
            Gerenciar Empresas
          </Button>
        </DialogTrigger>
        <DialogContent className="max-w-4xl max-h-[90vh] overflow-y-auto">
          <DialogHeader>
            <DialogTitle>Gerenciar Empresas</DialogTitle>
            <DialogDescription>
              Cadastre e gerencie as empresas construtoras e empreiteiras.
            </DialogDescription>
          </DialogHeader>

          <div className="space-y-6">
            <Form {...form}>
              <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <FormField
                    control={form.control}
                    name="name"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Nome da Empresa *</FormLabel>
                        <FormControl>
                          <Input placeholder="Construtora ABC" {...field} />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />

                  <FormField
                    control={form.control}
                    name="cnpj"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>CNPJ</FormLabel>
                        <FormControl>
                          <Input placeholder="00.000.000/0000-00" {...field} />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />

                  <FormField
                    control={form.control}
                    name="address"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Endereço da Sede</FormLabel>
                        <FormControl>
                          <Input placeholder="Rua, número" {...field} />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />

                  <FormField
                    control={form.control}
                    name="city"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Cidade</FormLabel>
                        <FormControl>
                          <Input placeholder="São Paulo" {...field} />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />

                  <FormField
                    control={form.control}
                    name="state"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Estado</FormLabel>
                        <FormControl>
                          <Input placeholder="SP" {...field} />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />

                  <FormField
                    control={form.control}
                    name="phone"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Telefone</FormLabel>
                        <FormControl>
                          <Input placeholder="(11) 98765-4321" {...field} />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />

                  <FormField
                    control={form.control}
                    name="email"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Email</FormLabel>
                        <FormControl>
                          <Input
                            type="email"
                            placeholder="contato@empresa.com"
                            {...field}
                          />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                </div>

                <div className="flex gap-2">
                  <Button type="submit" disabled={loading}>
                    {editingCompany ? "Atualizar" : "Cadastrar"} Empresa
                  </Button>
                  {editingCompany && (
                    <Button
                      type="button"
                      variant="outline"
                      onClick={() => {
                        setEditingCompany(null);
                        form.reset();
                      }}
                    >
                      Cancelar Edição
                    </Button>
                  )}
                </div>
              </form>
            </Form>

            <div className="border-t pt-4">
              <h3 className="text-lg font-semibold mb-4">Empresas Cadastradas</h3>
              {companies.length === 0 ? (
                <p className="text-muted-foreground text-center py-8">
                  Nenhuma empresa cadastrada ainda.
                </p>
              ) : (
                <Table>
                  <TableHeader>
                    <TableRow>
                      <TableHead>Nome</TableHead>
                      <TableHead>CNPJ</TableHead>
                      <TableHead>Cidade/Estado</TableHead>
                      <TableHead>Telefone</TableHead>
                      <TableHead className="text-right">Ações</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    {companies.map((company) => (
                      <TableRow key={company.id}>
                        <TableCell className="font-medium">{company.name}</TableCell>
                        <TableCell>{company.cnpj || "-"}</TableCell>
                        <TableCell>
                          {company.city && company.state
                            ? `${company.city}, ${company.state}`
                            : company.city || company.state || "-"}
                        </TableCell>
                        <TableCell>{company.phone || "-"}</TableCell>
                        <TableCell className="text-right">
                          <div className="flex justify-end gap-2">
                            <Button
                              variant="ghost"
                              size="sm"
                              onClick={() => setEditingCompany(company)}
                            >
                              <Pencil className="h-4 w-4" />
                            </Button>
                            <Button
                              variant="ghost"
                              size="sm"
                              onClick={() => setDeleteId(company.id)}
                            >
                              <Trash2 className="h-4 w-4" />
                            </Button>
                          </div>
                        </TableCell>
                      </TableRow>
                    ))}
                  </TableBody>
                </Table>
              )}
            </div>
          </div>
        </DialogContent>
      </Dialog>

      <AlertDialog open={!!deleteId} onOpenChange={() => setDeleteId(null)}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Confirmar exclusão</AlertDialogTitle>
            <AlertDialogDescription>
              Tem certeza que deseja excluir esta empresa? Esta ação não pode ser
              desfeita.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancelar</AlertDialogCancel>
            <AlertDialogAction onClick={handleDelete}>Excluir</AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </>
  );
};

export default CompaniesDialog;

package com.raylan.calculadoragorjetas.repository;

import java.util.List;

import com.raylan.calculadoragorjetas.model.Mesa;
import org.springframework.data.jpa.repository.JpaRepository;

public interface MesaRepository extends JpaRepository<Mesa, Long> {
    List<Mesa> findByAtivaTrueOrderByNumeroAsc();
    List<Mesa> findAllByOrderByNumeroAsc();
    boolean existsByNumero(Integer numero);
    boolean existsByNumeroAndIdNot(Integer numero, Long id);
}
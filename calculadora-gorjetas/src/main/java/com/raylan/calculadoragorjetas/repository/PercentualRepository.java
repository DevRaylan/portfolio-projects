package com.raylan.calculadoragorjetas.repository;

import java.util.List;

import com.raylan.calculadoragorjetas.model.Percentual;
import org.springframework.data.jpa.repository.JpaRepository;

public interface PercentualRepository extends JpaRepository<Percentual, Long> {
    List<Percentual> findByAtivoTrue();
}
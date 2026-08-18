<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Contracts\CabBookingRepositoryInterface;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use App\Repositories\Contracts\FollowUpRepositoryInterface;
use App\Repositories\Contracts\HotelRepositoryInterface;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Repositories\Contracts\LeadRepositoryInterface;
use App\Repositories\Contracts\PackageRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\QuotationRepositoryInterface;
use App\Repositories\Contracts\ResortRepositoryInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use App\Repositories\Contracts\VendorRepositoryInterface;
use App\Repositories\Contracts\VillaRepositoryInterface;

use App\Repositories\Eloquent\BookingRepository;
use App\Repositories\Eloquent\CabBookingRepository;
use App\Repositories\Eloquent\CustomerRepository;
use App\Repositories\Eloquent\ExpenseRepository;
use App\Repositories\Eloquent\FollowUpRepository;
use App\Repositories\Eloquent\HotelRepository;
use App\Repositories\Eloquent\InvoiceRepository;
use App\Repositories\Eloquent\LeadRepository;
use App\Repositories\Eloquent\PackageRepository;
use App\Repositories\Eloquent\PaymentRepository;
use App\Repositories\Eloquent\QuotationRepository;
use App\Repositories\Eloquent\ResortRepository;
use App\Repositories\Eloquent\RoleRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\VehicleRepository;
use App\Repositories\Eloquent\VendorRepository;
use App\Repositories\Eloquent\VillaRepository;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(LeadRepositoryInterface::class, LeadRepository::class);
        $this->app->bind(FollowUpRepositoryInterface::class, FollowUpRepository::class);
        $this->app->bind(PackageRepositoryInterface::class, PackageRepository::class);
        $this->app->bind(HotelRepositoryInterface::class, HotelRepository::class);
        $this->app->bind(ResortRepositoryInterface::class, ResortRepository::class);
        $this->app->bind(VillaRepositoryInterface::class, VillaRepository::class);
        $this->app->bind(BookingRepositoryInterface::class, BookingRepository::class);
        $this->app->bind(QuotationRepositoryInterface::class, QuotationRepository::class);
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
        $this->app->bind(InvoiceRepositoryInterface::class, InvoiceRepository::class);
        $this->app->bind(ExpenseRepositoryInterface::class, ExpenseRepository::class);
        $this->app->bind(VendorRepositoryInterface::class, VendorRepository::class);
        $this->app->bind(VehicleRepositoryInterface::class, VehicleRepository::class);
        $this->app->bind(CabBookingRepositoryInterface::class, CabBookingRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
    }
}

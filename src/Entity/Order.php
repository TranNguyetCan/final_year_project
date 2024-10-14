<?php

namespace App\Entity;

use App\Enum\OrderStatus;
use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    private ?string $deliveryLocal = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $total = null;

    #[ORM\Column(type: Types::STRING, enumType: OrderStatus::class)]
    private OrderStatus $status;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderDetail::class)]
    private Collection $orderDetails;
    
    #[ORM\Column(length: 255)]
    private ?string $cusName = null;


    #[ORM\Column]
    private ?int $cusPhone = null;

    #[ORM\Column]
    private ?string $paymentMethod = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $username = null;


    #[ORM\ManyToOne(inversedBy: 'orderLst')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Voucher $vouchers = null;

    public function __construct()
    {
        $this->orderDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDeliveryLocal(): ?string
    {
        return $this->deliveryLocal;
    }

    public function setDeliveryLocal(string $deliveryLocal): self
    {
        $this->deliveryLocal = $deliveryLocal;

        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setPaymentMethod(string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function setTotal(string $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function setStatus(OrderStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, OrderDetail>
     */
    public function getOrderDetails(): Collection
    {
        return $this->orderDetails;
    }

    public function addOrderDetail(OrderDetail $orderDetail): self
    {
        if (!$this->orderDetails->contains($orderDetail)) {
            $this->orderDetails->add($orderDetail);
            $orderDetail->setOrders($this);
        }

        return $this;
    }

    public function removeOrderDetail(OrderDetail $orderDetail): self
    {
        if ($this->orderDetails->removeElement($orderDetail)) {
            // set the owning side to null (unless already changed)
            if ($orderDetail->getOrders() === $this) {
                $orderDetail->setOrders(null);
            }
        }

        return $this;
    }

    public function getCusName(): ?string
    {
        return $this->cusName;
    }

    public function setCusName(string $cusName): self
    {
        $this->cusName = $cusName;

        return $this;
    }

    public function getCusPhone(): ?int
    {
        return $this->cusPhone;
    }

    public function setCusPhone(int $cusPhone): self
    {
        $this->cusPhone = $cusPhone;

        return $this;
    }

    public function getUsername(): ?User
    {
        return $this->username;
    }

    public function setUsername(?User $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getVouchers(): ?Voucher
    {
        return $this->vouchers;
    }

    public function setVouchers(?Voucher $vouchers): self
    {
        $this->vouchers = $vouchers;

        return $this;
    }
}

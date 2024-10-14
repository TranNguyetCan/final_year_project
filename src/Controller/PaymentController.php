<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Entity\CreditCard;
use App\Entity\Paypal;
use App\Entity\ProSize;
use App\Entity\User;
use App\Enum\OrderStatus;
use App\Form\OrderType;
use App\Form\PaymentType;
use App\Form\CreditCardType;
use App\Form\PaypalType;
use App\Form\UserType;
use App\Repository\CartRepository;
use App\Repository\OrderDetailRepository;
use App\Repository\OrderRepository;
use App\Repository\PaymentRepository;
use App\Repository\ProductRepository;
use App\Repository\ProSizeRepository;
use App\Repository\UserRepository;
use App\Repository\VoucherRepository;
use App\Service\PaypalService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

date_default_timezone_set('Asia/Ho_Chi_Minh');
class PaymentController extends AbstractController
{
    private $em;
    private $kernel;
    private OrderRepository $repo;
    private $paypalService;
    public function __construct(OrderRepository $repo, EntityManagerInterface $em, KernelInterface $kernel, PaypalService $paypalService)
    {
        $this->repo = $repo;
        $this->em = $em;
        $this->kernel = $kernel;
        $this->paypalService = $paypalService;
    }

    // Show payment page
    /**
     * @Route("/payment", name="payment_page", methods={"POST", "GET"})
     */
    public function paymentAction(Request $req,CartRepository $repoCart, UserRepository $repoUser, VoucherRepository $voucherRepository,
    CartRepository $cartRepo,OrderRepository $orderRepo, ProSizeRepository $proSizeRepo, OrderDetailRepository $orderDetailRepository): Response
    {
        $order = new Order();
        $orderForm = $this->createForm(PaymentType::class, $order);
        $orderForm->handleRequest($req);
        // if ($orderForm->isSubmitted() && $orderForm->isValid()) {
    
        //     // $cartItems = $cartRepo->findAll();
        //     // if ($cartItems) {
        //     //     //tạo order và lưu vào db
        //     //     $total = 0;
        //     //     $order->setUsername($this->getUser());
        //     //     $order->setDate(new \DateTime());//lấy ngày hiện tại
        //     //     $order->setStatus(OrderStatus::Ordered);
        //     //     //Lưu order lần 1 để tạo đơn hàng và lấy mã đơn hàng đưa vô OrderDetail
        //     //     $orderRepo->save($order, true);
                
        //     //     foreach ($cartItems as $cartItem) {
        //     //         //lấy prosize từ cart
        //     //         $proSize = $proSizeRepo->find($cartItem->getProSize()->getId());
        //     //         $total += $proSize->getProduct()->getPrice() * $cartItem->getCount();
        //     //         //lưu orderDetail lại
        //     //         $orderDetailRepository->addProductToOrder($order, $proSize, $cartItem->getCount());

        //     //         //xóa cart
        //     //         $cartRepo->remove($cartItem, true);
        //     //     }
        //     //     $totalDiscount = $total - ($total * ($order->getVouchers()->getPercentage() / 100));
                
        //     //     $order->setTotal($totalDiscount);
        //     //     //Lưu order lần cuối để chốt đơn hàng
        //     //     $orderRepo->save($order, true);
        //     // }
        //     return $this->redirectToRoute('redirectPaymentMethodAction');
        // }
        $user = $this->getUser();
        $products = $repoCart->showCart($user);
        $vouchers = $voucherRepository->findAll();

        return $this->render('payment/index.html.twig', [
            // Display product and Calculate the total price
            'products' => $products,
            'vouchers' => $vouchers,
            // Display customer's infomation to set into Order
            'user' => $user,
            'orderForm' => $orderForm->createView(  )
        ]);
    }



    
    /**
     * @Route("/redirect_payment_method", name="redirectPaymentMethodAction", methods={"POST"})
     */
    public function redirectPaymentMethodAction(Request $req, CartRepository $cartRepo, ProSizeRepository $proSizeRepo,  OrderDetailRepository $orderDetailRepository, VoucherRepository $voucherRepository): Response {
        $order = new Order();
        $form = $this->createForm(PaymentType::class, $order);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
    
            $cartItems = $cartRepo->findAll();
            if ($cartItems) {
                $paymentMethod = $form->get('paymentMethod')->getData();

                if($paymentMethod == "paypal"){
                    $total = 0.0;
                    foreach ($cartItems as $cartItem) {
                        //lấy prosize từ cart
                        $proSize = $proSizeRepo->find($cartItem->getProSize()->getId());
                        $total += $proSize->getProduct()->getPrice() * $cartItem->getCount();
                        //lưu orderDetail lại
                        // $orderDetailRepository->addProductToOrder($order, $proSize, $cartItem->getCount());
                    }
                    $totalDiscounted = $total - ($total * ($order->getVouchers()->getPercentage() / 100));
                    // Lưu vào session
                    $req->getSession()->set('totalAmount', number_format($totalDiscounted, 2));

                    return $this->createPayment($req);
                    // return $this->redirectToRoute('handledPaypal');
                }

                if($paymentMethod == "COD"){
                    return $this->redirectToRoute('addOrder');
                }
            }
        }
            
        // return $this->redirectToRoute('orderConfirm', ['orderId' => $order->getId()]);
    }

    

    //Paypal
    /**
     * @Route("/paypal", name="handledPaypal", methods={"POST"})
     */
    public function createPayment(Request $req): Response
    {
        $totalAmount = $req->getSession()->get('totalAmount');
        // Kiểm tra xem tổng số tiền có tồn tại và có phải là số hợp lệ không
        if (!$totalAmount || !is_numeric($totalAmount)) {
            throw $this->createNotFoundException('No valid payment amount found in session.');
        }

        // Chuyển đổi tổng số tiền thành kiểu float
        $totalAmount = (float) $totalAmount;
        if(!is_numeric($totalAmount)){
            throw $this->createNotFoundException('Total amount cannot cast to float');
        }

        $payment = $this->paypalService->createPayment($totalAmount, 'USD', 'Product Description', 'https://127.0.0.1:8000/paypal/success', 'https://127.0.0.1:8000//payment');

        return $this->redirect($payment->getApprovalLink());
    }

    /**
     * @Route("/paypal/success", name="paypal_success")
     */
    public function paymentSuccess(Request $request, OrderRepository $orderRepo, CartRepository $cartRepo,
    ProSizeRepository $proSizeRepo, OrderDetailRepository $orderDetailRepository, VoucherRepository $voucherRepository): Response
    {
        $paymentId = $request->query->get('paymentId');
        $payerId = $request->query->get('PayerID');

        $payment = $this->paypalService->executePayment($paymentId, $payerId);

        $order = new Order();
        $cartItems = $cartRepo->findAll();
        if ($cartItems) {
            //tạo order và lưu vào db
            $total = 0.0;
            $order->setUsername($this->getUser());
            $order->setDate(new \DateTime());//lấy ngày hiện tại
            $order->setPaymentMethod('paypal');
            $order->setStatus(OrderStatus::Ordered);
            //Lưu order lần 1 để tạo đơn hàng và lấy mã đơn hàng đưa vô OrderDetail
            $orderRepo->save($order, true);
            
            foreach ($cartItems as $cartItem) {
                //lấy prosize từ cart
                $proSize = $proSizeRepo->find($cartItem->getProSize()->getId());
                $total += $proSize->getProduct()->getPrice() * $cartItem->getCount();
                //lưu orderDetail lại
                $orderDetailRepository->addProductToOrder($order, $proSize, $cartItem->getCount());

                //xóa cart
                $cartRepo->remove($cartItem, true);
            }
            // $totalDiscount = $total - ($total * ($order->getVouchers()->getPercentage() / 100));
            
            $order->setTotal($total);
            //Lưu order lần cuối để chốt đơn hàng
            $orderRepo->save($order, true);
        }

        // Xử lý khi thanh toán thành công
        return $this->render('payment/paymentSuccess.html.twig', [
            'orderId' => $order->getId()
        ]);
    }

    /**
     * @Route("/paypal/cancel", name="paypal_cancel")
     */
    public function paymentCancel(): Response
    {
        // Xử lý khi người dùng hủy thanh toán
        return $this->render('payment/cancel.html.twig');
    }



    
    /**
     * @Route("/checkout", name="checkout")
     */
    public function checkout(Request $request, PaymentRepository $repo): Response
    {
        $paymentform = $this->createForm(PaymentType::class);

        $paymentform->handleRequest($request);
            $listPayment = $repo->findAll();
    
            // Process data when form is submitted
            // $formData = $paymentform->getData();
            // $selectedPayment = $formData['pa'];
            return $this->render('payment/choosePayment.html.twig', ['listPayment' => $listPayment]);
            // xử lý theo phương thức thanh toán được chọn
            
            // return $this->redirectToRoute('payment');
    }


   


}

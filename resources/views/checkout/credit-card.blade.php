<!DOCTYPE html>
<html lang="en">

<head>
    <title>Stripe Payment</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
    @php
        $stripe_key = env('STRIPE_KEY', 'somedefaultvalue');
    @endphp
    <div class="container" style="margin-top:10%;margin-bottom:10%">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="">
                    <p>You will be charged rs 100</p>
                </div>
                <div class="card">
                    <form action="{{ url('submitcheckout') }}" method="post" id="payment-form">
                        @csrf

                        <div class="form-group">
                            <div class="card-header">
                                <label for="card-element">
                                    Enter your credit card information
                                </label>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <label>Card Holder Name <span class="text-danger">*</span></label> <span id="card-holder-name-info"
                                            class="info"></span><br>
                                        <input type="text" id="fullname" name="fullname"
                                            class="demoInputBox form-control" placeholder="Enter Card Holder Name"
                                            required>
                                    </div>
                                    <div class="col">
                                        <label>Email</label> <span class="text-danger">*</span> <span id="email-info" class="info"></span><br>
                                        <input type="email" id="email" name="email" class="demoInputBox form-control"
                                            required placeholder="Enter Email">
                                    </div>
                                </div>
                                </br>
                                <div class="row">
                                    <div class="col">
                                        <div id="card-element">
                                            <!-- A Stripe Element will be inserted here. -->
                                        </div>
                                        <!-- Used to display form errors. -->
                                        <div id="card-errors" role="alert"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button id="card-button" class="btn btn-dark" type="submit"
                                data-secret="{{ $intent }}"> Pay </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        // Custom styling can be passed to options when creating an Element.
        // (Note that this demo uses a wider set of styles than the guide below.)

        var style = {
            base: {
                color: '#32325d',
                lineHeight: '18px',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };

        const stripe = Stripe('{{ $stripe_key }}', {
            locale: 'en'
        }); // Create a Stripe client.
        const elements = stripe.elements(); // Create an instance of Elements.
        const cardElement = elements.create('card', {
            style: style
        }); // Create an instance of the card Element.
        const cardButton = document.getElementById('card-button');
        const clientSecret = cardButton.dataset.secret;

        cardElement.mount('#card-element'); // Add an instance of the card Element into the `card-element` <div>.

        // Handle real-time validation errors from the card Element.
        cardElement.addEventListener('change', function(event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        // Handle form submission.
        var form = document.getElementById('payment-form');
        var fullname = document.getElementById('fullname');
        var email = document.getElementById('email');
        var card = document.querySelector("[name='cardnumber']");        

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            stripe.handleCardPayment(clientSecret, cardElement, {
                    payment_method_data: {
                        card: card,
                        billing_details: { 
                            name: fullname,
                            email: email,
                         }
                    }
                })
                .then(function(result) {
                    console.log('result');
                    console.log(result);
                    if (result.error) {
                        // Inform the user if there was an error.
                        var errorElement = document.getElementById('card-errors');
                        errorElement.textContent = result.error.message;
                    } else {
                        console.log(result);
                        form.submit();
                    }
                });
        });
    </script>
</body>

</html>
